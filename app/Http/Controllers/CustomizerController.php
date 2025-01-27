<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;
use \App\Models\Event;
use \App\Models\Action;
use \App\Models\Page;
use File;
use Schema;
use AbstractMySQLDriver;

use \App\Models\ActionField;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Str;
use DB;

class CustomizerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $models = [];
        $path = app_path('Models');
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $models[] = $filename;
        }

        return view('customizer.index', compact('models'));
    }

    public function create()
    {
        return view('customizer.create');
    }

    public function store(Request $request)
    {
        $modelName = str_replace(' ', '', $request->input('model_name'));
        $fields = $request->input('fields');
        $fieldTypes = $request->input('field_types');
        $relationships = $request->input('relation_fields');
        $relationTypes = $request->input('relation_types');
        $relatedModels = $request->input('related_models');
        $events = $request->input('events');
        $eventTypes = $request->input('event_types');
        $actions = $request->input('actions');
        $actionTypes = $request->input('action_types');
        $pages = $request->input('pages');
        $pageContents = $request->input('page_contents');
        $pageUrls = $request->input('page_urls');

        // Generate model and migration
        $fieldsArray=$fields;
        //$fieldsArray = array_merge($fields, $relationships);
        Artisan::call('generate:model', [
            'name' => $modelName,
            'fields' => $fieldsArray
        ]);

        // Store events
        // foreach ($events as $index => $event) {
        //     Event::create([
        //         'model_id' => $modelName,
        //         'name' => $event,
        //         'event_type_id' => $eventTypes[$index]
        //     ]);
        // }

        // // Store actions
        // foreach ($actions as $index => $action) {
        //     Action::create([
        //         'model_id' => $modelName,
        //         'name' => $action,
        //         'action_type_id' => $actionTypes[$index]
        //     ]);
        // }

        // // Store pages
        // foreach ($pages as $index => $page) {
        //     Page::create([
        //         'model_id' => $modelName,
        //         'name' => $page,
        //         'content' => $pageContents[$index],
        //         'url' => $pageUrls[$index]
        //     ]);
        // }

        return redirect()->route('customizer.index');
    }

    public function edit(Request $request)
    {
        $modelName=$request->get('model_name');
        $model=$modelName;

        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get basic column information
        $columns = Schema::getColumnListing($tableName);

        $fields = [];
        foreach ($columns as $column) {
            if(!in_array($column, ['id', 'created_at', 'updated_at'])){
            $fields[] = [
                'name' => $column,
                'type' => Schema::getColumnType($tableName, $column),
                'required'=>true,
                'default'=>'',
            ];
            }
        }

        // Dummy data for relationships, events, actions, pages
        $relationships = [];
        $events = [];
        $actions = [];
        $pages = [];

        // Retrieve available models
        $availableModels = $this->getAvailableModels();

        $relationships = $this->getModelRelationships($model);

        $actions = Action::where('model_name', $modelName)->get();

        $actions->each(function ($action) {
            $action->data = json_decode($action->data, true);
        });

        return view('customizer.edit', [
            'modelName' => $modelName,
            'model'=>$modelName,
            'fields' => $fields,
            'relationships' => $relationships,
            'availableModels'=>$availableModels,
            'events' => $events,
            'actions' => $actions,
            'pages' => $pages,
        ]);
    }

    public function edit_fields(Request $request)
    {
        $modelName=$request->get('model_name');
        $model=$modelName;

        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get basic column information
        $columns = Schema::getColumnListing($tableName);

        $fields = [];

        // Get the $search_fields array from the model file
        $reflector = new \ReflectionClass($modelClass);
        $modelContent = file_get_contents($reflector->getFileName());
        preg_match('/public\s+static\s+\$search_fields\s+=\s+\[(.*?)\];/s', $modelContent, $matches);
        $searchFieldsArray = [];
        if (isset($matches[1])) {
            $searchFieldsArray = array_map('trim', explode(',', str_replace(["'", " "], '', $matches[1])));
        }

        foreach ($columns as $column) {
            if(!in_array($column, ['id', 'created_at', 'updated_at'])){
            $fields[] = [
                'name' => $column,
                'type' => Schema::getColumnType($tableName, $column),
                'required'=>true,
                'default'=>'',
                'search_field' => in_array($column, $searchFieldsArray),
            ];
            }
        }

        // Dummy data for relationships, events, actions, pages
        $relationships = [];
        $events = [];
        $actions = [];
        $pages = [];

        // Retrieve available models
        $availableModels = $this->getAvailableModels();

        $relationships = $this->getModelRelationships($model);

        $actions = Action::where('model_name', $modelName)->get();

        $actions->each(function ($action) {
            $action->data = json_decode($action->data, true);
        });

        return view('customizer.edit_fields', [
            'modelName' => $modelName,
            'model'=>$modelName,
            'fields' => $fields,
            'relationships' => $relationships,
            'availableModels'=>$availableModels,
            'events' => $events,
            'actions' => $actions,
            'pages' => $pages,
        ]);
    }

    public function edit_relations(Request $request)
    {
        $modelName=$request->get('model_name');
        $model=$modelName;

        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get basic column information
        $columns = Schema::getColumnListing($tableName);

        $fields = [];
        foreach ($columns as $column) {
            if(!in_array($column, ['id', 'created_at', 'updated_at'])){
            $fields[] = [
                'name' => $column,
                'type' => Schema::getColumnType($tableName, $column),
                'required'=>true,
                'default'=>'',
            ];
            }
        }

        // Dummy data for relationships, events, actions, pages
        $relationships = [];
        $events = [];
        $actions = [];
        $pages = [];

        // Retrieve available models
        $availableModels = $this->getAvailableModels();

        $relationships = $this->getModelRelationships($model);

        $relationMappingConfig = $this->getExistingRelationMappingConfig($modelInstance);

        $actions = Action::where('model_name', $modelName)->get();

        $actions->each(function ($action) {
            $action->data = json_decode($action->data, true);
        });

        return view('customizer.edit_relations', [
            'modelName' => $modelName,
            'model'=>$modelName,
            'fields' => $fields,
            'relationships' => $relationships,
            'availableModels'=>$availableModels,
            'relationMappingConfig' => $relationMappingConfig,
            'events' => $events,
            'actions' => $actions,
            'pages' => $pages,
        ]);
    }

    public function edit_events(Request $request)
    {
        $modelName=$request->get('model_name');
        $model=$modelName;

        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get basic column information
        $columns = Schema::getColumnListing($tableName);

        $fields = [];
        foreach ($columns as $column) {
            if(!in_array($column, ['id', 'created_at', 'updated_at'])){
            $fields[] = [
                'name' => $column,
                'type' => Schema::getColumnType($tableName, $column),
                'required'=>true,
                'default'=>'',
            ];
            }
        }

        // Dummy data for relationships, events, actions, pages
        $relationships = [];
        $existingEvents = \App\Models\Event::where('model_name', $modelName)->get();
        $actions = [];
        $pages = [];

        // Retrieve available models
        $availableModels = $this->getAvailableModels();

        $relationships = $this->getModelRelationships($model);

        $relationSpecificFields=[];

        foreach ($relationships as $relation) {
            // Get the related model class from the relation structure
            $relatedModelClass = $relation['related_model']; // e.g., "App\Models\Area"

            // Check if the related model class exists
            if (class_exists($relatedModelClass)) {
                // Fetch columns for the related model's table
                $relatedModelInstance = new $relatedModelClass;
                $relationSpecificFields[$relation['name']] = Schema::getColumnListing($relatedModelInstance->getTable());
            }
        }

        return view('customizer.edit_events', [
            'model' => $modelName,
            'modelFields' => $fields,
            'relationships' => $relationships,
            'existingEvents' => $existingEvents,
            'relationSpecificFields'=>$relationSpecificFields
        ]);
    }

    public function edit_actions(Request $request)
    {
        $modelName=$request->get('model_name');
        $model=$modelName;

        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get basic column information
        $columns = Schema::getColumnListing($tableName);

        $fields = [];
        foreach ($columns as $column) {
            if(!in_array($column, ['id', 'created_at', 'updated_at'])){
            $fields[] = [
                'name' => $column,
                'type' => Schema::getColumnType($tableName, $column),
                'required'=>true,
                'default'=>'',
            ];
            }
        }

        // Dummy data for relationships, events, actions, pages
        $relationships = [];
        $events = [];
        $actions = [];
        $pages = [];

        // Retrieve available models
        $availableModels = $this->getAvailableModels();

        $relationships = $this->getModelRelationships($model);

        $actions = Action::where('model_name', $modelName)->get();

        $actions->each(function ($action) {
            $action->data = json_decode($action->data, true);
        });

        return view('customizer.edit_actions', [
            'modelName' => $modelName,
            'model'=>$modelName,
            'fields' => $fields,
            'relationships' => $relationships,
            'availableModels'=>$availableModels,
            'events' => $events,
            'actions' => $actions,
            'pages' => $pages,
        ]);
    }

    public function edit_pages(Request $request)
    {
        $modelName=$request->get('model_name');
        $model=$modelName;

        $relationships = $this->getModelRelationships($model);

        $hasManyRelations = collect($relationships)->filter(function ($relation) {
            return $relation['type'] === 'HasMany';  // Filter hasMany relationships
        });

        // Get the model name from the request
        $modelName = $request->get('model_name');
        $modelClass = 'App\\Models\\' . $modelName;

        // Check if the model class exists
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        // Fetch the existing pages related to the model
        $existingPages = \App\Models\Page::where('model_name', $modelName)->get();

        // Fetch the relationships for the model
        // Assuming you have relationships set in your models, we'll extract them
        $modelInstance = new $modelClass;


        // If there's no getRelations method, you could manually define the relationships
        // or use an alternative method to fetch them.

        // Pass the necessary data to the view
        return view('customizer.edit_pages', [
            'model' => $modelName,
            'existingPages' => $existingPages,
            'relationships' => $hasManyRelations
        ]);


    }

    private function getExistingRelationMappingConfig($modelInstance)
    {
        if (method_exists($modelInstance, 'getRelationMappingConfig')) {
            return $modelInstance::getRelationMappingConfig();
        }

        return [
            'sourceModel' => [],
            'targetModel' => [],
            'fieldMapping' => [],
        ];
    }

    private function getModelRelationships($modelName)
    {
        $modelClass = 'App\\Models\\' . $modelName;

        if (!class_exists($modelClass)) {
            return [];
        }

        $model = new $modelClass;
        $reflector = new \ReflectionClass($model);
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

        $relationships = [];

        foreach ($methods as $method) {
            if ($method->class == $modelClass && $method->getNumberOfParameters() == 0) {
                try {
                    $returnType = $method->invoke($model);

                    if ($returnType instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                        $relationType = (new \ReflectionClass($returnType))->getShortName();
                        $relatedModel = get_class($returnType->getRelated());
                        // $foreignKey = method_exists($returnType, 'getForeignKeyName') ? $returnType->getForeignKeyName() : null;
                        // $localKey = method_exists($returnType, 'getLocalKeyName') ? $returnType->getLocalKeyName() : null;

                        if ($relationType === 'BelongsTo') {
                            // BelongsTo relationships typically have the foreign key on the current model
                            $foreignKey = $returnType->getForeignKeyName();
                            $localKey = $returnType->getOwnerKeyName();
                        } elseif ($relationType === 'HasMany' || $relationType === 'HasOne' || $relationType === 'MorphMany') {
                            // HasMany/HasOne/MorphMany typically have the foreign key on the related model
                            $foreignKey = $returnType->getForeignKeyName();
                            $localKey = $returnType->getLocalKeyName();
                        } else {
                            // Fallback if the methods are not available
                            $foreignKey = method_exists($returnType, 'getForeignKeyName') ? $returnType->getForeignKeyName() : null;
                            $localKey = method_exists($returnType, 'getLocalKeyName') ? $returnType->getLocalKeyName() : 'id';
                        }

                        $relationships[] = [
                            'name' => $method->getName(),
                            'type' => $relationType,
                            'related_model' => $relatedModel,
                            'model_key' => $foreignKey,
                            'related_model_key' => $localKey,
                        ];
                    }
                } catch (\Throwable $e) {
                    // Handle or log the error if needed
                }
            }
        }

        return $relationships;
    }


    private function getAvailableModels()
    {
        $models = [];
        $modelPath = app_path('Models'); // Assuming your models are in app/Models directory

        // Scan the directory for PHP files (models)
        foreach (File::allFiles($modelPath) as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $models[] = $filename;
        }

        return $models;
    }

    // Placeholder methods, replace with actual logic
    private function getRelationships($modelInstance)
    {
        // Implement logic to fetch model relationships
        return [
            // Example data
            ['name' => 'user', 'type' => 'belongsTo', 'related_model' => 'User'],
        ];
    }

    private function getEvents($modelInstance)
    {
        // Implement logic to fetch model events
        return [
            // Example data
            ['name' => 'created', 'type' => 'type1'],
        ];
    }

    private function getActions($modelInstance)
    {
        // Implement logic to fetch model actions
        return [
            // Example data
            ['name' => 'sendEmail', 'type' => 'type1'],
        ];
    }

    private function getPages($modelInstance)
    {
        // Implement logic to fetch model pages
        return [
            // Example data
            ['name' => 'home', 'content' => 'Welcome to the homepage', 'url' => '/home'],
        ];
    }

    public function update(Request $request)
    {
        $modelName=$request->get('model_name');
        $modelClass = 'App\\Models\\' . $modelName;
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get existing fields
        $existingFields = Schema::getColumnListing($tableName);
        $existingFields = array_diff($existingFields, ['id', 'created_at', 'updated_at']);

        // Process the fields from the request
        $newFields = $request->input('fields', []);

        // Arrays to hold actions
        $fieldsToAdd = [];
        $fieldsToUpdate = [];
        $fieldsToDelete = array_diff($existingFields, array_column($newFields, 'name'));

        foreach ($newFields as $field) {
            if (in_array($field['name'], $existingFields)) {
                // Field exists, check if it needs updating
                $fieldsToUpdate[] = $field;
            } else {
                // New field
                $fieldsToAdd[] = $field;
            }
        }

         // Generate migration
        try {
            $migrationPath = $this->generateMigration($tableName, $fieldsToAdd, $fieldsToUpdate, $fieldsToDelete);

            // Run the migration
            Artisan::call('migrate');

                // Update the model file
            $this->updateModelFile($modelClass, $fieldsToAdd, $fieldsToUpdate, $fieldsToDelete);

            $this->updateRelationships($request->input('relationships', []), $modelName);

            $this->updateActions($request, $modelName);


            return redirect()->route('customizer.index')->with('success', 'Model updated successfully.');
        } catch (\Exception $e) {
            // If an error occurs, delete the migration file
            if (isset($migrationPath) && File::exists($migrationPath)) {
                File::delete($migrationPath);
            }

            // Redirect back to the edit page with the error message
            return redirect()->route('customizer.edit', ['model' => $modelName])
                             ->withErrors('Error during migration: ' . $e->getMessage())
                             ->withInput();
        }
    }

    private function mapColumnType($mysqlType)
    {
        if (str_contains($mysqlType, 'int')) {
            return 'integer';
        } elseif (str_contains($mysqlType, 'varchar')) {
            return 'string';
        } elseif (str_contains($mysqlType, 'text')) {
            return 'text';
        } elseif (str_contains($mysqlType, 'tinyint(1)')) {
            return 'boolean';
        } elseif (str_contains($mysqlType, 'datetime')) {
            return 'datetime';
        }
        // Add more mappings as necessary

        return 'string'; // Default to string if no match
    }

    private function needsUpdate($existingField, $newField)
    {
        return $existingField['type'] !== $newField['type'] ||
               $existingField['required'] !== $newField['required'] ||
               (isset($newField['default']) && $existingField['default'] !== $newField['default']);
    }

    public function update_fields(Request $request)
    {
        $modelName=$request->get('model_name');
        $modelClass = 'App\\Models\\' . $modelName;
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get existing fields
        $existingFields = Schema::getColumnListing($tableName);
        $existingFields = array_diff($existingFields, ['id', 'created_at', 'updated_at']);

        // Process the fields from the request
        $newFields = $request->input('fields', []);

        // Arrays to hold actions
        $fieldsToAdd = [];
        $fieldsToUpdate = [];
        $fieldsToDelete = array_diff($existingFields, array_column($newFields, 'name'));

        $existingFieldAttributes = [];

        $searchFields = [];  // Array to hold fields marked for search

        $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");

        foreach ($columns as $column) {
            $req='optional';
            if($column->Null==='YES'){
                $req='optional';
            }else{
                $req='required';
            }
            $existingFieldAttributes[$column->Field] = [

                'type' => $this->mapColumnType($column->Type),
                'required' => $req,
                'default' => $column->Default,
            ];
        }

        // check if which needs to be added or to be updated
        foreach ($newFields as $field) {
            if (in_array($field['name'], $existingFields)) {
               if (isset($existingFieldAttributes[$field['name']])) {
                if ($this->needsUpdate($existingFieldAttributes[$field['name']], $field)) {
                    // Field exists, check if it needs updating
                    $fieldsToUpdate[] = $field;
                }
                }
            } else {
                // New field
                $fieldsToAdd[] = $field;
            }

            // Check if the field is marked for search
            if (isset($field['search_field']) && $field['search_field']) {
                $searchFields[] = $field['name'];
            }
        }
         // Generate migration
        try {
            $migrationPath = $this->generateMigration($tableName, $fieldsToAdd, $fieldsToUpdate, $fieldsToDelete);

            // Run the migration
            Artisan::call('migrate');

                // Update the model file
            $this->updateModelFile($modelClass, $fieldsToAdd, $fieldsToUpdate, $fieldsToDelete, $searchFields);

            // $this->updateRelationships($request->input('relationships', []), $modelName);

            // $this->updateActions($request, $modelName);


            return redirect()->back()->with('success', 'Model updated successfully.');
        } catch (\Exception $e) {
            // If an error occurs, delete the migration file
            if (isset($migrationPath) && File::exists($migrationPath)) {
                File::delete($migrationPath);
            }

            // Redirect back to the edit page with the error message
            return redirect()->route('customizer.edit', ['model' => $modelName])
                             ->withErrors('Error during migration: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function update_relations(Request $request)
    {
        $modelName=$request->get('model_name');
        $modelClass = 'App\\Models\\' . $modelName;
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();


         // update relation
        try {
            // Update relationships
            $this->updateRelationships($request->input('relationships', []), $modelName);

            // Update relation mapping config
            $this->updateRelationMappingConfig($request->input('relationMappingConfig', []), $modelName);

            return redirect()->back()->with('success', 'Model updated successfully.');
        } catch (\Exception $e) {
            // If an error occurs, delete the migration file
            // if (isset($migrationPath) && File::exists($migrationPath)) {
            //     File::delete($migrationPath);
            // }

            // Redirect back to the edit page with the error message
            return redirect()->route('customizer.edit', ['model' => $modelName])
                             ->withErrors('Error during migration: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function update_events(Request $request)
    {
        $modelName = $request->get('model_name');
        $modelClass = 'App\\Models\\' . $modelName;

        // Check if the model exists
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        try {
            // Fetch event data from the request
            $eventNames = $request->input('event_name', []);
            $eventTypes = $request->input('event_type', []);
            $scopes = $request->input('scope', []);
            $fields = $request->input('field', []);
            $relationFields = $request->input('relation_field', []);
            $relationSpecificFields = $request->input('relation_specific_field', []);

            // Get existing events for the model
            $existingEvents = \App\Models\Event::where('model_name', $modelName)->get();

            // Keep track of updated event names
            $updatedEventNames = [];

            foreach ($eventNames as $index => $eventName) {
                $eventType = $eventTypes[$index];
                $scope = $scopes[$index];

                // Determine the field based on the type and scope
                $field = ($eventType === 'update' && $scope === 'entire_model') ? $fields[$index] : null;
                $relationField = ($scope === 'specific_relation') ? $relationFields[$index] : null;
                $relationSpecificField = ($eventType == 'update' && $scope == 'specific_relation') ? $relationSpecificFields[$index] : null;
                // Update or create the event
                \App\Models\Event::updateOrCreate(
                    [
                        'model_name' => $modelName,
                        'name' => $eventName, // Use the name to ensure uniqueness
                    ],
                    [
                        'page_name' => $request->get('page_name', null), // Optional page name
                        'type' => $eventType,
                        'scope' => $scope,
                        'field' => $field,
                        'relation_field' => $relationField,
                        'relation_specific_field' => $relationSpecificField, // Store the relation-specific field
                    ]
                );

                $updatedEventNames[] = $eventName; // Track the updated event names
            }

            // Handle deletion of old events that are not in the updated event names list
            $existingEvents->whereNotIn('name', $updatedEventNames)->each(function ($event) {
                $event->delete();
            });

            // Redirect with success message
            return redirect()->back();

        } catch (\Exception $e) {
            var_dump($relationSpecificFields);
            exit;
            // Handle errors and redirect with the error message
            return $e->getMessage();
        }
    }

    public function update_actions(Request $request)
    {
        $modelName=$request->input('model_name');
        $modelClass = 'App\\Models\\' . $modelName;
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

        // Get existing fields
        // $existingFields = Schema::getColumnListing($tableName);
        // $existingFields = array_diff($existingFields, ['id', 'created_at', 'updated_at']);

        // // Process the fields from the request
        // $newFields = $request->input('fields', []);

        // // Arrays to hold actions
        // $fieldsToAdd = [];
        // $fieldsToUpdate = [];
        // $fieldsToDelete = array_diff($existingFields, array_column($newFields, 'name'));

        // foreach ($newFields as $field) {
        //     if (in_array($field['name'], $existingFields)) {
        //         // Field exists, check if it needs updating
        //         $fieldsToUpdate[] = $field;
        //     } else {
        //         // New field
        //         $fieldsToAdd[] = $field;
        //     }
        // }

         // Generate migration
        try {

            $modelName = $request->input('model_name');
            $actions = $request->input('actions', []);

            // Retrieve existing actions from the database
            $existingActions = Action::where('model_name', $modelName)->get()->keyBy('id')->toArray();

            // Delete actions not in the current request
            $existingActionIds = array_column($actions, 'id');
            Action::where('model_name', $modelName)->whereNotIn('id', $existingActionIds)->delete();

            foreach ($actions as $actionIndex => $actionData) {
                if (isset($actionData['id']) && isset($existingActions[$actionData['id']])) {
                    // Update existing action

                    if($actionData['action_type']=='update_record'){
                        $targetModel=$actionData['update_model'];
                    }

                    if($actionData['action_type']=='create_record'){
                        $targetModel=$actionData['create_model'];
                    }

                    if($actionData['action_type']=='delete_record'){
                        $targetModel=$actionData['delete_model'];
                    }
                    $action = Action::find($actionData['id']);
                    $action->update([
                        'event_type' => $actionData['event_type'],
                        'action_type' => $actionData['action_type'],
                        'target_model' => $targetModel??null,
                        'condition' => $actionData['condition'] ?? null,
                    ]);
                } else {
                    if($actionData['action_type']=='update_record'){
                        $targetModel=$actionData['update_model'];
                    }

                    if($actionData['action_type']=='create_record'){
                        $targetModel=$actionData['create_model'];
                    }
                    if($actionData['action_type']=='delete_record'){
                        $targetModel=$actionData['delete_model'];
                    }
                    // Create new action
                    $action = Action::create([
                        'model_name' => $modelName,
                        'event_type' => $actionData['event_type'],
                        'action_type' => $actionData['action_type'],
                        'target_model' => $targetModel??null,
                        'condition' => $actionData['condition'] ?? null,
                    ]);
                }

                // Handle action fields (update_fields or create_fields)
                $fieldKey = $actionData['action_type'] == 'create_record' ? 'create_fields' : 'update_fields';
                // $this->processActionFields($action, $actionData[$fieldKey] ?? []);
                 // Handle action fields (update_fields, create_fields, delete_conditions)
                if ($actionData['action_type'] == 'delete_record') {
                    // Handle delete specific logic here
                    $this->processDeleteConditions($action, $actionData['delete_conditions'] ?? []);
                } else if($actionData['action_type'] == 'update_record' || $actionData['action_type'] == 'create_record') {
                    $this->processActionFields($action, $actionData[$fieldKey] ?? []);
                }else if ($actionData['action_type'] == 'send_notification') {
                    $this->processNotification($action, $actionData['notification'] ?? []);
                }else{

                }
            }


            return redirect()->back()->with('success', 'Actions updated successfully.');
        } catch (\Exception $e) {
            // If an error occurs, delete the migration file
            // if (isset($migrationPath) && File::exists($migrationPath)) {
            //     File::delete($migrationPath);
            // }
            var_dump($e->getMessage());
            exit;

            // Redirect back to the edit page with the error message
            return redirect()->route('customizer.edit', ['model' => $modelName])
                             ->withErrors('Error during migration: ' . $e->getMessage())
                             ->withInput();
        }
    }


    private function processDeleteConditions(Action $action, array $conditions)
    {
        // Implement the logic to process and save the delete conditions
        // Example:
        foreach ($conditions as $condition) {
            if(\App\Models\ActionCondition::where('action_id', $action->id)->where('field_name', $condition['field'])->first()){
                \App\Models\ActionCondition::where('action_id', $action->id)->update([
                    'action_id'=>$action->id,
                    'field_name' => $condition['field'],
                    'operator' => $condition['operator'],
                    'value' => $condition['value'],
                ]);
            }else{
                \App\Models\ActionCondition::create([
                    'action_id'=>$action->id,
                    'field_name' => $condition['field'],
                    'operator' => $condition['operator'],
                    'value' => $condition['value'],
                ]);
            }
        }
    }

    private function processNotification(Action $action, array $notificationData)
    {
        // Implement the logic to process and save the notification data
        if(\App\Models\ActionNotification::where('action_id', $action->id)->first()){
            \App\Models\ActionNotification::where('action_id', $action->id)->update([
            'action_id'=>$action->id,
            'recipient' => $notificationData['recipient'],
            'subject' => $notificationData['subject'],
            'message' => $notificationData['message'],
        ]);
        }else{
            \App\Models\ActionNotification::create([
            'action_id'=>$action->id,
            'recipient' => $notificationData['recipient'],
            'subject' => $notificationData['subject'],
            'message' => $notificationData['message'],
        ]);
        }

    }

    private function processActionFields(Action $action, array $fields)
    {
        $existingFields = $action->fields->keyBy('id')->toArray();

        $fieldIds = array_column($fields, 'id');
        ActionField::where('action_id', $action->id)->whereNotIn('id', $fieldIds)->delete();

        foreach ($fields as $fieldIndex=>$fieldData) {
            if (isset($fieldData['id']) && isset($existingFields[$fieldData['id']])) {
                // Update existing field
                ActionField::find($fieldData['id'])->update([
                    'field_name' => $fieldData['field'],
                    'value_source' => $fieldData['value_source'],
                    'static_value' => $fieldData['value_source'] === 'static' ? $fieldData['value'] : null,
                    'current_model_field' => $fieldData['value_source'] === 'current_model_field' ? $fieldData['value'] : null,
                    'related_model_relation' => $fieldData['value_source'] === 'related_model_field' ? $fieldData['related_model_relation'] : null,
                    'related_model_field' => $fieldData['value_source'] === 'related_model_field' ? $fieldData['value_source'] : null,
                ]);
            } else {
                // Create new field
                ActionField::create([
                    'action_id'=>$action->id,
                    'field_name' => $fieldData['field'],
                    'value_source' => $fieldData['value_source'],
                    'static_value' => $fieldData['value_source'] === 'static' ? $fieldData['value'] : null,
                    'current_model_field' => $fieldData['value_source'] === 'current_model_field' ? $fieldData['value'] : null,
                    'related_model_relation' => $fieldData['value_source'] === 'related_model_field' ? $fieldData['related_model_relation'] : null,
                    'related_model_field' => $fieldData['value_source'] === 'related_model_field' ? $fieldData['value_source'] : null,
                ]);
            }
        }


    }

    public function update_pages(Request $request)
    {
        $modelName=$request->get('model_name');
        $modelClass = 'App\\Models\\' . $modelName;
        if (!class_exists($modelClass)) {
            return redirect()->route('customizer.index')->withErrors("Model {$modelName} does not exist.");
        }

        $modelInstance = new $modelClass;
        $tableName = $modelInstance->getTable();

         // Generate migration
        try {

            $existingPages = \App\Models\Page::where('model_name', $modelName)->get()->keyBy('name');
            $updatedPageNames = [];

            $modelName = $request->input('model_name');
            $pages = $request->input('pages');
            $paths = $request->input('paths');
            $pageTypes = $request->input('page_type');
            $relations = $request->input('relation');
            $customCodes = $request->input('custom_code');


            foreach ($pages??[] as $index => $pageName) {
                $pageType = $pageTypes[$index];
                $path=$paths[$index];
                $relation = $relations[$index] ?? null;

                $existPage = \App\Models\Page::where('model_name', $modelName)
                                        ->where('name', $pageName)->orwhere('url', $path)
                                        ->first();

                // Store or update the page in the database
                if($existPage){
                    $existPage->update(
                        [
                            'model_name' => $modelName,
                            'name' => $pageName,
                            'url'=>$path,
                            'entire_model'=>$pageType,
                            'model_relation'=>$relation,
                            'custom_code'=>$pageTypes[$index] == 2 ? $customCodes[$index] : null,
                        ]
                    );
                }else{
                    \App\Models\Page::create(
                        [
                            'entire_model' => $pageType,
                            'model_relation' => $relation,
                            'url' => $path,
                            'name'=>$pageName,
                            'model_name'=>$modelName,
                            'custom_code'=>$pageTypes[$index] == 2 ? $customCodes[$index] : null,
                        ]
                    );
                }

                $updatedPageNames[] = $pageName;
            }

            // Handle any pages that were removed in the update process
            $existingPages->whereNotIn('name', $updatedPageNames)->each(function($page) {
                $page->delete();
            });

            return redirect()->back()->with('success', 'Model updated successfully.');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    private function updateRelationMappingConfig($relationMappingConfig, $modelName)
    {
        $modelClass = 'App\\Models\\' . $modelName;
        $reflector = new \ReflectionClass($modelClass);
        $modelPath = $reflector->getFileName();

        $modelContent = file_get_contents($modelPath);

        // Generate the content for getRelationMappingConfig method
        $relationMappingContent = $this->generateRelationMappingConfig($relationMappingConfig);

        // Insert or update the getRelationMappingConfig method in the model
        $modelContent = $this->insertRelationMappingConfigMethodContent($modelContent, $relationMappingContent, 'getRelationMappingConfig');

        // Save the updated model content back to the file
        file_put_contents($modelPath, $modelContent);
    }

    private function generateRelationMappingConfig($relationMappingConfig)
    {
        $sourceModelConfig = '';
        $targetModelConfig = '';
        $fieldMappingConfig = '';

        if (isset($relationMappingConfig['sourceModel'])) {
            foreach ($relationMappingConfig['sourceModel'] as $sourceModel) {
                $sourceModelConfig .= sprintf(
                    "'%s' => ['target_field' => '%s', 'local_field' => '%s'],\n",
                    $sourceModel['model_name'],
                    $sourceModel['target_field'],
                    $sourceModel['local_field']
                );
            }
        }

        if (isset($relationMappingConfig['targetModel'])) {
            foreach ($relationMappingConfig['targetModel'] as $targetModel) {
                $targetModelConfig .= sprintf(
                    "'%s' => ['target_field' => '%s', 'local_field' => '%s'],\n",
                    $targetModel['model_name'],
                    $targetModel['target_field'],
                    $targetModel['local_field']
                );
            }
        }

        if (isset($relationMappingConfig['fieldMapping'])) {
            foreach ($relationMappingConfig['fieldMapping'] as $fieldMapping) {
                $fieldMappingConfig .= sprintf(
                    "'%s' => '%s',\n",
                    $fieldMapping['source_field'],
                    $fieldMapping['target_field']
                );
            }
        }

        $relationMappingContent = <<<EOD

        public static function getRelationMappingConfig()
        {
            return [
                'sourceModel' => [
                    $sourceModelConfig
                ],
                'targetModel' => [
                    $targetModelConfig
                ],
                'fieldMapping' => [
                    $fieldMappingConfig
                ],
            ];
        }

    EOD;

        return $relationMappingContent;
    }


    private function insertRelationMappingConfigMethodContent($modelContent, $methodContent, $methodName)
    {
        $pattern = '/public\s+static\s+function\s+' . $methodName . '\s*\(.*?\)\s*{.*?}/s';

        if (preg_match($pattern, $modelContent)) {
            // Replace the existing method with the new content
            $modelContent = preg_replace($pattern, $methodContent, $modelContent);
        } else {
            // Insert the method content before the last closing bracket
            $modelContent = preg_replace('/}\s*$/', "\n" . $methodContent . "\n}", $modelContent);
        }

        return $modelContent;
    }

    private function generateMigration($tableName, $fieldsToAdd, $fieldsToUpdate, $fieldsToDelete)
    {

        if (empty($fieldsToAdd) && empty($fieldsToUpdate) && empty($fieldsToDelete)) {
            echo "No changes detected. Migration not created.\n";
            return;
        }

        $timestamp = date('Y_m_d_His');
        $migrationName = $timestamp.'_update_' . $tableName . '_table';
        $migrationPath = database_path("migrations/{$migrationName}.php");

        // Ensure the class name is unique by appending the timestamp
        $migrationClassName = 'Update' . Str::studly($tableName) . 'Table';

        $migrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\n";
        $migrationContent .= "return new class extends Migration\n{\n";
        $migrationContent .= "    public function up()\n    {\n";
        $migrationContent .= "        Schema::table('{$tableName}', function (Blueprint \$table) {\n";

        // Add new fields
        foreach ($fieldsToAdd as $field) {
            $migrationContent .= "            \$table->{$field['type']}('{$field['name']}')";
            if (!empty($field['required'])) {
                if($field['required']=='optional'){
                    $migrationContent .= "->nullable()";
                }
            }
            if (isset($field['default'])) {
                $migrationContent .= "->default('{$field['default']}')";
            }
            $migrationContent .= ";\n";
        }

        // Update existing fields
        foreach ($fieldsToUpdate as $field) {
            $migrationContent .= "            \$table->{$field['type']}('{$field['name']}')->change();\n";
        }

        // Delete fields
        foreach ($fieldsToDelete as $field) {
            $migrationContent .= "            \$table->dropColumn('{$field}');\n";
        }

        $migrationContent .= "        });\n";
        $migrationContent .= "    }\n\n";

        $migrationContent .= "    public function down()\n    {\n";
        $migrationContent .= "        Schema::table('{$tableName}', function (Blueprint \$table) {\n";

        // Rollback logic for added fields
        foreach ($fieldsToAdd as $field) {
            $migrationContent .= "            \$table->dropColumn('{$field['name']}');\n";
        }

        // Rollback logic for deleted fields (you'll need to manually define the original type if you want to rollback a deletion)
        foreach ($fieldsToDelete as $field) {
            $migrationContent .= "            // \$table->string('{$field}'); // This would need to match the original schema\n";
        }

        $migrationContent .= "        });\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "};\n";

        file_put_contents($migrationPath, $migrationContent);
    }

    private function updateModelFile($modelClass, $fieldsToAdd, $fieldsToUpdate, $fieldsToDelete, $searchFields)
    {

        $reflector = new \ReflectionClass($modelClass);
        $modelPath = $reflector->getFileName();

        $modelContent = file_get_contents($modelPath);

        // Update search_fields
        $searchFieldsPattern = '/public\s+static\s+\$search_fields\s+=\s+\[(.*?)\];/s';
        if (preg_match($searchFieldsPattern, $modelContent, $matches)) {
            $searchFieldsArray = $this->parseArrayFromString($matches[1]);

            foreach ($fieldsToAdd as $field) {
                if (in_array($field['name'], $searchFields)) {
                    $searchFieldsArray[] = $field['name'];
                }
            }

            foreach ($fieldsToDelete as $field) {
                if (($key = array_search($field, $searchFieldsArray)) !== false) {
                    unset($searchFieldsArray[$key]);
                }
            }

            $newSearchFieldsContent = 'public static $search_fields = [' . implode(', ', array_map(function ($field) {
                return "'{$field}'";
            }, $searchFields)) . '];';

            $modelContent = preg_replace($searchFieldsPattern, $newSearchFieldsContent, $modelContent);
            file_put_contents($modelPath, $modelContent);
        } else {
            // If $search_fields does not exist, add it
            $newSearchFieldsContent = "\npublic static \$search_fields = [" . implode(', ', array_map(function ($field) {
                return "'{$field}'";
            }, $searchFields)) . "];\n";

            $modelContent = preg_replace('/}\s*$/', "\n" . $newSearchFieldsContent . "\n}", $modelContent);
            file_put_contents($modelPath, $modelContent);
        }

        if (empty($fieldsToAdd) && empty($fieldsToUpdate) && empty($fieldsToDelete)) {
            echo "No changes detected. Migration not created.\n";
            return;
        }

        // Update $fillable
        $fillablePattern = '/protected\s+\$fillable\s+=\s+\[(.*?)\];/s';
        if (preg_match($fillablePattern, $modelContent, $matches)) {
            $fillableArray = $this->parseArrayFromString($matches[1]);

            foreach ($fieldsToAdd as $field) {
                $fillableArray[] = $field['name'];
            }

            foreach ($fieldsToDelete as $field) {
                if (($key = array_search($field, $fillableArray)) !== false) {
                    unset($fillableArray[$key]);
                }
            }

            $newFillableContent = 'protected $fillable = [' . implode(', ', array_map(function ($field) {
                return "'{$field}'";
            }, $fillableArray)) . '];';

            $modelContent = preg_replace($fillablePattern, $newFillableContent, $modelContent);
        }

        // Update readableColumnNames method
        $readablePattern = '/public\s+static\s+function\s+readableColumnNames\(\)\s*\{(.*?)\}/s';
        if (preg_match($readablePattern, $modelContent, $matches)) {
            $readableArray = $this->parseAssociativeArrayFromString($matches[1]);

            foreach ($fieldsToAdd as $field) {
                $readableArray[$field['name']] = ucwords(str_replace('_', ' ', $field['name']));
            }

            foreach ($fieldsToDelete as $field) {
                unset($readableArray[$field]);
            }

            $newReadableContent = 'public static function readableColumnNames() {' . "\n";
            $newReadableContent .= '        return [' . "\n";

            foreach ($readableArray as $key => $value) {
                $newReadableContent .= "            '{$key}' => '{$value}',\n";
            }

            $newReadableContent .= '        ];' . "\n";
            $newReadableContent .= '    }';

            $modelContent = preg_replace($readablePattern, $newReadableContent, $modelContent);
        }

        // Update requiredFields method
        $requiredPattern = '/public\s+static\s+function\s+requiredFields\(\)\s*\{(.*?)\}/s';
        if (preg_match($requiredPattern, $modelContent, $matches)) {
            $requiredArray = $this->parseArrayFromString($matches[1]);

            foreach ($fieldsToAdd as $field) {
                if ($field['required']) {
                    $requiredArray[] = $field['name'];
                }
            }

            foreach ($fieldsToDelete as $field) {
                if (($key = array_search($field, $requiredArray)) !== false) {
                    unset($requiredArray[$key]);
                }
            }

            $newRequiredContent = 'public static function requiredFields() {' . "\n";
            $newRequiredContent .= '        return [' . "\n";

            foreach ($requiredArray as $field) {
                $newRequiredContent .= "            '{$field}',\n";
            }

            $newRequiredContent .= '        ];' . "\n";
            $newRequiredContent .= '    }';

            $modelContent = preg_replace($requiredPattern, $newRequiredContent, $modelContent);
        }

        // Save the updated model content back to the file
        file_put_contents($modelPath, $modelContent);
    }

    private function generateRelationMethodName($modelClass)
    {
        $model = new $modelClass;
        $reflector = new \ReflectionClass($model);
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        $relationMap = [];

        foreach ($methods as $method) {
            if ($method->class == $modelClass && $method->getNumberOfParameters() == 0) {
                try {
                    $returnType = $method->invoke($model);

                    if ($returnType instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                        // Get the relationship's foreign key
                        $foreignKey = $returnType->getForeignKeyName();
                        // Get the related model's name
                        $relationModel = class_basename(get_class($returnType->getRelated()));

                        // Map the foreign key to the relationship method name (studly case)
                        $relationMap[$foreignKey] = $relationModel;
                    }
                } catch (\Throwable $e) {
                    // Handle or log the error if needed
                }
            }
        }

        // Generate the method content
        $relationMethodContent = <<<EOD

        public function getRelationMethodName(\$foreignKey)
        {
            \$relationMap = [
    EOD;

        foreach ($relationMap as $key => $value) {
            $relationMethodContent .= "            '{$key}' => '{$value}',\n";
        }

        $relationMethodContent .= <<<EOD
            ];

            return \$relationMap[\$foreignKey] ?? null;
        }

    EOD;

    $relationMethodContent .= <<<EOD

        public function getRelationMappings()
        {
           return \$relationMap = [
    EOD;

        foreach ($relationMap as $key => $value) {
            $relationMethodContent .= "            '{$key}' => '{$value}',\n";
        }

        $relationMethodContent .= <<<EOD
            ];
        }

    EOD;

        return $relationMethodContent;
    }


    private function generateHasManyRelations($modelClass)
{
    $model = new $modelClass;
    $reflector = new \ReflectionClass($model);
    $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
    $hasManyRelations = [];

    foreach ($methods as $method) {
        $methodName = $method->getName();

        if ($method->class == $modelClass && $method->getNumberOfParameters() == 0) {
            try {
                // Invoke the method to see if it returns a HasMany relationship
                $returnType = $method->invoke($model);

                if ($returnType instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                    $hasManyRelations[$methodName] = '$this->' . $methodName . '()';
                }
            } catch (\Throwable $e) {
                // Handle or log the error if needed
            }
        }
    }

    // Generate the method content
    $hasManyRelationsContent = <<<EOD

    public function getHasManyRelations()
    {
        return [
EOD;

    foreach ($hasManyRelations as $key => $value) {
        $hasManyRelationsContent .= "            '{$key}' => {$value},\n";
    }

    $hasManyRelationsContent .= <<<EOD
        ];
    }

EOD;

    return $hasManyRelationsContent;
}



    private function parseArrayFromString($string)
    {
        $array = [];
        $items = explode(',', str_replace('];', '', str_replace('return [', '', $string)));
        foreach ($items as $item) {
            $item = trim($item, " \t\n\r\0\x0B'\"");
            if (!empty($item)) {
                $array[] = $item;
            }
        }
        return $array;
    }

    private function parseAssociativeArrayFromString($string)
    {
        $array = [];
        $items = explode(',', str_replace('];', '', str_replace('return [', '', $string)));
        foreach ($items as $item) {
            $itemParts = explode('=>', $item);
            if (count($itemParts) == 2) {
                $key = trim($itemParts[0], " \t\n\r\0\x0B'\"");
                $value = trim($itemParts[1], " \t\n\r\0\x0B'\"");
                $array[$key] = $value;
            }
        }
        return $array;
    }

    private function updateRelationships($relationships, $modelName)
    {
        $modelClass = 'App\\Models\\' . $modelName;
        $reflector = new \ReflectionClass($modelClass);
        $modelPath = $reflector->getFileName();

        $modelContent = file_get_contents($modelPath);

        // Extract existing relationships
        $existingRelationships = $this->getExistingRelationships($modelContent);

        // Update or add new relationships
        foreach ($relationships as $relationship) {
            $name = $relationship['name'];
            $type = $relationship['type'];
            $modelKey = $relationship['model_key'];
            $relatedModelKey = $relationship['related_model_key'];
            $relatedModel= $relationship['related_model'];

            $relationshipMethod = $this->generateRelationshipMethod($name, $type, $modelKey, $relatedModelKey, $relatedModel);

            if (isset($existingRelationships[$name])) {
                // Replace existing relationship
                $modelContent = str_replace($existingRelationships[$name], $relationshipMethod, $modelContent);
            } else {
                // Add new relationship
                $modelContent = preg_replace('/}\s*$/', "\n" . $relationshipMethod . "\n}", $modelContent);
            }
        }

        // Remove deleted relationships
        foreach ($existingRelationships as $name => $method) {
            if (!array_key_exists($name, array_column($relationships, 'name', 'name'))) {
                $modelContent = str_replace($method, '', $modelContent);
            }
        }

        // Save the updated model content back to the file
        file_put_contents($modelPath, $modelContent);

        // Now generate and insert/update the getRelationMethodName method
        $relationMethodContent = $this->generateRelationMethodName($modelClass);
        $modelContent = $this->insertMethodContent($modelContent, $relationMethodContent, 'getRelationMethodName');

        // Now generate and insert/update the getHasManyRelations method
        $hasManyRelationsContent = $this->generateHasManyRelations($modelClass);
        $modelContent = $this->insertMethodContent($modelContent, $hasManyRelationsContent, 'getHasManyRelations');

        // Save the updated model content back to the file after inserting relation methods
        file_put_contents($modelPath, $modelContent);
    }

    private function insertMethodContent($modelContent, $methodContent, $methodName)
    {
        // Check if the method already exists
        $pattern = '/public\s+function\s+' . $methodName . '\s*\(.*?\)\s*{.*?}/s';

        if (preg_match($pattern, $modelContent)) {
            // Replace the existing method with the new content
            $modelContent = preg_replace($pattern, $methodContent, $modelContent);
        } else {
            // Insert the method content before the last closing bracket
            $modelContent = preg_replace('/}\s*$/', "\n" . $methodContent . "\n}", $modelContent);
        }

        return $modelContent;
    }

    private function getExistingRelationships($modelContent)
    {
        $pattern = '/public function (\w+)\(\)\s*\{(.*?)\}/s';
        preg_match_all($pattern, $modelContent, $matches, PREG_SET_ORDER);

        $relationships = [];
        foreach ($matches as $match) {
            $relationships[$match[1]] = $match[0]; // $match[1] is the relationship name, $match[0] is the full method
        }
        return $relationships;
    }

    private function generateRelationshipMethod($name, $type, $modelKey, $relatedModelKey, $relatedModelName)
    {
        return <<<EOT

        public function {$name}()
        {
            return \$this->{$type}({$relatedModelName}::class, '{$modelKey}', '{$relatedModelKey}');
        }

    EOT;
    }


    private function updateActions(Request $request, $modelName)
    {
        $existingActions = Action::where('model_name', $modelName)->get();
        $submittedActions = collect($request->actions);

        // Handle update or create actions
        foreach ($submittedActions as $actionData) {
            if (isset($actionData['id'])) {
                $this->updateExistingAction($actionData, $modelName);
            } else {
                $this->createNewAction($actionData, $modelName);
            }
        }

        // Handle deletions
        $submittedActionIds = $submittedActions->pluck('id')->filter();
        $existingActions->whereNotIn('id', $submittedActionIds)->each(function ($action) {
            $this->deleteAction($action);
        });
    }

    private function createNewAction(array $actionData, $modelName)
    {
        $action = new Action();
        $this->fillActionData($action, $actionData, $modelName);
        $action->save();
    }

    private function updateExistingAction(array $actionData, $modelName)
    {
        $action = Action::find($actionData['id']);
        if ($action) {
            $this->fillActionData($action, $actionData, $modelName);
            $action->save();
        }
    }

    private function deleteAction(Action $action)
    {
        $action->delete();
    }

    private function fillActionData(Action $action, array $actionData, $modelName)
    {
        $action->model_name = $modelName;
        $action->name = $actionData['name'];
        $action->action_type = $actionData['action_type'];
        $action->event = $actionData['event'];
        $action->url = $actionData['url'] ?? null;
        $action->email_title = $actionData['email_title'] ?? null;
        $action->email_content = $actionData['email_content'] ?? null;
        $action->notification_content = $actionData['notification_content'] ?? null;
        $action->custom_code = $actionData['content'] ?? null;
        $action->where_model = $actionData['where']['model'] ?? null;
        $action->where_field = $actionData['where']['field'] ?? null;
        $action->where_value = $actionData['where']['value'] ?? null;
        $action->where_custom_value = $actionData['where']['custom_value'] ?? null;

        $action->data = isset($actionData['data']) ? json_encode($actionData['data']) : null;
    }

}
