<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Validator;
use DB;
use Illuminate\Support\Str;

class AutoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function getModel($model)
    {
        $modelClass = 'App\\Models\\' . ucfirst($model);
        if (!class_exists($modelClass)) {
            abort(404, 'Model not found.');
        }
        return new $modelClass;
    }

    public function create($model)
    {
        $modelInstance = $this->getModel($model);
        // $fields = Schema::getColumnListing($modelInstance->getTable());
        $fields=array_diff(Schema::getColumnListing($modelInstance->getTable()), $modelInstance->getHidden());
        $readableColumns = $modelInstance::readableColumnNames();
        $relationsData = $this->getRelationsData($modelInstance);
        $fields = array_keys($readableColumns);
        $requiredFields = $modelInstance::requiredFields();

        // Code to show the create form with fields
        return view('create', compact('model', 'fields', 'modelInstance', 'readableColumns', 'relationsData', 'requiredFields'));
    }

    public function store(Request $request, $model)
    {
        $modelInstance = $this->getModel($model);
        $modelInstance->fill($request->except('_token'));
        $modelInstance->save();


        $hasManyRelations = $modelInstance->getHasManyRelations();

        foreach ($hasManyRelations as $relationName => $relationInstance) {
            // Check if the request has data for the relation
            if ($request->has($relationName)) {
                $relatedItemsData = $request->input($relationName);

                foreach ($relatedItemsData as $relatedItemData) {
                    // Create each related model record and associate it with the main model
                    $relatedModelInstance = $relationInstance->getRelated();
                    $newRelatedItem = new $relatedModelInstance();
                    $newRelatedItem->fill($relatedItemData);

                    // Associate the new related item with the main model
                    $modelInstance->{$relationName}()->save($newRelatedItem);
                }
            }
        }

        $this->syncRelatedItems($modelInstance)??[];
        // Redirect or return response
        return redirect("/model/$model");
    }

    public function syncRelatedItems($modelInstance)
    {
        $config = $modelInstance::getRelationMappingConfig();

        if(isset($config['sourceModel'])){
            // Extract the configurations
            $sourceConfig = $config['sourceModel'];
            $targetConfig = $config['targetModel'];
            $fieldMapping = $config['fieldMapping'];

            // Loop through the source models to get the related items
            foreach ($sourceConfig as $sourceRelation => $details) {
                $sourceField = $details['target_field'];
                $localField = $details['local_field'];

                // Fetch the source items based on the relationship between source and the current model
                $sourceModelClass = "App\Models\\{$sourceRelation}";

                $sourceModelInstance = new $sourceModelClass(); // Create an instance of the source model
                $sourceTableName = $sourceModelInstance->getTable();

                $sourceItems = $sourceModelClass::join($modelInstance->getTable(), $sourceTableName.'.'.$sourceField, '=', $modelInstance->getTable().'.'.$localField)->get();

                // Now loop through the target models to create related items
                foreach ($targetConfig as $targetRelation => $targetDetails) {
                    $targetModelClass = "App\Models\\{$targetRelation}";

                    foreach ($sourceItems as $sourceItem) {
                        $mappedData = [];

                        // Map the fields from the source model to the target model
                        foreach ($fieldMapping as $sourceField => $targetField) {
                            $mappedData[$targetField] = $sourceItem->$sourceField;
                        }

                        // Include the current model's primary key as the foreign key in the target model
                        $mappedData[$targetDetails['target_field']] = $modelInstance->getKey();  // Set picking_job_id to the current PickingJob's ID

                        // Create the target item (e.g., PickingItem)
                        $targetModelClass::create($mappedData);
                    }
                }
            }
        }
    }

    public function edit($model, $id)
    {
        $modelInstance = $this->getModel($model)->findOrFail($id);
        //$fields = Schema::getColumnListing($modelInstance->getTable());
         $fields = array_diff(Schema::getColumnListing($modelInstance->getTable()), $modelInstance->getHidden());
         $readableColumns = $modelInstance::readableColumnNames();
         $relationsData = $this->getRelationsData($modelInstance);
         $fields = array_keys($readableColumns);
         $requiredFields = $modelInstance::requiredFields();
        // Code to show the edit form with fields
        return view('edit', compact('model', 'modelInstance', 'fields', 'readableColumns', 'relationsData', 'requiredFields'));
    }

    public function update(Request $request, $model, $id)
    {
        $modelInstance = $this->getModel($model)->findOrFail($id);
        $modelInstance->fill($request->except('_token', '_method'));
        $modelInstance->save();

        // Redirect or return response
        return redirect("/model/$model");
    }

    public function destroy($model, $id)
    {
        $modelInstance = $this->getModel($model)->findOrFail($id);
        $modelInstance->delete();

        // Redirect or return response
        return redirect("/model/$model");
    }

    public function index(Request $request, $model)
    {
        // $modelInstance = $this->getModel($model);
        // $data = $modelInstance->all();

        // $fillable = $modelInstance->getFillable();
        // $timestamps = ['created_at', 'updated_at'];
        // //$fields = array_merge($fillable, $timestamps);
        // $relationsData = $this->getRelationsData($modelInstance);

        // $readableColumns = $modelInstance::readableColumnNames();
        // $fields = array_keys($readableColumns);

        // $fields = array_diff(array_merge($fillable, $timestamps), $modelInstance->getHidden());
        // // Code to list all records
        // return view('index', compact('model', 'data', 'fields', 'relationsData', 'readableColumns'));

        $modelInstance = $this->getModel($model);
        $fillable = $modelInstance->getFillable();
        $timestamps = ['created_at', 'updated_at'];

        $relationsData = $this->getRelationsData($modelInstance);

        // Define the readable column names
        $readableColumns = $modelInstance::readableColumnNames();

        $fields = array_keys($readableColumns);
        $fields = array_diff(array_merge($fillable, $timestamps), $modelInstance->getHidden());


        // Start the query
        $query = $modelInstance->newQuery();

        // Get the declared search fields from the model
        $searchFields = $modelInstance::$search_fields ?? [];

        // Apply filters dynamically
        foreach ($searchFields as $field) {
            if (in_array($field, $fields)) {
                if ($request->filled($field)) {
                    if (array_key_exists($field, $relationsData)) {
                        // For relational fields, filter by the related model's ID
                        $query->where($field, $request->input($field));
                    } else {
                        // For normal fields, use a like query
                        $query->where($field, 'like', '%' . $request->input($field) . '%');
                    }
                }
            }
        }


        // Handle sorting
        $sortBy = $request->input('sort_by', 'id'); // Default sort by ID
        $sortDirection = $request->input('sort_direction', 'asc'); // Default sort direction

        $query->orderBy($sortBy, $sortDirection);

        // Get the filtered data
        $data = $query->paginate(10);

        $data->appends($request->all());

        $pages=\App\Models\Page::where('model_name', $model)->get();

        // Code to list all records
        return view('index', compact('model', 'data', 'pages', 'searchFields', 'fields', 'relationsData', 'readableColumns', 'sortBy', 'sortDirection'));
    }

    public function show($model, $id)
    {
        $modelInstance = $this->getModel($model)->findOrFail($id);

        $relationsData = $this->getRelationsData($modelInstance);

        $readableColumns = $modelInstance::readableColumnNames();
        $fields = array_keys($readableColumns);

        $events=\App\Models\Event::where('model_name', $model)->get();

        // Code to show a single record
        return view('show', compact('model', 'modelInstance', 'events', 'relationsData', 'readableColumns', 'fields'));
    }

    public function quick_update(Request $request)
    {
        try {
            // Get the required data from the request
            $field = $request->input('field');
            $model = $request->input('model');
            $id = $request->get('id');
            $value = $request->input('value');

            // Dynamically resolve the model
            $modelClass = '\\App\\Models\\' . ucfirst($model);
            if (!class_exists($modelClass)) {
                return response()->json(['error' => 'Invalid model'], 400);
            }

            // Find the model instance by id
            $modelInstance = $modelClass::where('id', $id)->first();
            if (!$modelInstance) {
                return response()->json(['error' => 'Model not found'], 404);
            }

            // Check if the field exists in the model's fillable attributes
            if (!in_array($field, $modelInstance->getFillable())) {
                return response()->json(['error' => 'Field not allowed'], 400);
            }
            // Update the field with the new value
            $modelInstance->update([$field => $value]);

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Field updated successfully']);
        } catch (\Exception $e) {
        }
    }


    public function show_page(Request $request, $model, $id, $page)
    {
        $pageDetails = \App\Models\Page::where('model_name', $model)->where('name', $page)->firstOrFail();
        if($pageDetails->entire_model == 0) {

            $modelInstance = $this->getModel($model)->findOrFail($id);

            $relationsData = $this->getRelationsData($modelInstance);

            $readableColumns = $modelInstance::readableColumnNames();
            $fields = array_keys($readableColumns);


            // For entire model, just show the model data as usual
            return view('show', compact('model', 'modelInstance', 'relationsData', 'readableColumns', 'fields'));

        } elseif ($pageDetails->entire_model == 1) {
            $modelInstance = $this->getModel($model)->findOrFail($id);
            // For specific relation, retrieve the related data
            $relationName = $pageDetails->model_relation; // Assuming 'relation' is the name of the relation

            $relatedData = $modelInstance->$relationName()->get(); // Fetch the related data
            // Get readable columns for the related model (relation)
            $relatedModelClass = $modelInstance->$relationName()->getRelated();
            $readableColumns = $relatedModelClass::readableColumnNames();
            $fields = array_keys($readableColumns);

            // Pass the related data to the view
            return view('show_page', compact('model', 'modelInstance', 'relatedData', 'relationName', 'readableColumns', 'fields'));
        } elseif ($pageDetails->entire_model == 2){
            return eval($this->prepareDynamicCode($pageDetails->custom_code, $request));
        }else{
             // Ensure the input is sanitized
        }

    }

    private function prepareDynamicCode($code, $request)
    {
        // Provide context to the code
        $contextCode = '
        $request = app(\'' . Request::class . '\');
        ';

        // Return the concatenated code for evaluation
        return $contextCode . $code;
    }

    public function isHasManyRelation($relation)
    {
        if (method_exists($this, $relation)) {
            $relationInstance = $this->{$relation}();
            return $relationInstance instanceof HasMany;
        }
        return false;
    }

    protected function getRelationsData($modelInstance)
    {
        $relationsData = [];
        foreach ((new \ReflectionClass($modelInstance))->getMethods() as $method) {
            if ($method->class == get_class($modelInstance) && $method->isPublic() && $method->getNumberOfParameters() == 0) {
                try {
                    $relation = $modelInstance->{$method->name}();
                    if ($relation instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                        $relatedModel = $relation->getRelated();
                        $foreignKey = $relation->getForeignKeyName();
                        $relationsData[$foreignKey] = $relatedModel::all();
                    }
                } catch (\Exception $e) {
                    // Skip methods that are not relations
                }
            }
        }
        return $relationsData;
    }

    public function bulk_import($model){
        $modelInstance = $this->getModel($model);
        $fields=array_diff(Schema::getColumnListing($modelInstance->getTable()), $modelInstance->getHidden());
        return view('bulk_import')->with(['model'=>$model, 'fields'=>$fields]);
    }

    public function bulk_import_save(Request $request, $model){

        // Validate that a file has been uploaded
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Fetch the model dynamically (ensure model mapping if you're using different model names)
        $modelClass = 'App\Models\\' . ucfirst($model);
        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model not found');
        }

        // Read CSV file with PHP's native fgetcsv()
        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        // Assuming the first row is the header
        $headers = fgetcsv($file);

        $bulkInsertData = [];

        // Loop through each row of the CSV
        while ($row = fgetcsv($file)) {
            $rowData = [];

            // Match columns with header fields
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? null; // Handle missing data in CSV
            }

            $bulkInsertData[] = $rowData;
        }

        fclose($file);

        // Insert data in bulk
        if (!empty($bulkInsertData)) {
            DB::table((new $modelClass)->getTable())->insert($bulkInsertData);
        }

        return redirect()->back()->with('success', 'Bulk import successfully completed.');

    }


    // protected function getRelationsData($modelInstance)
    // {
    //     $relationsData = [];
    //     foreach ((new \ReflectionClass($modelInstance))->getMethods() as $method) {
    //         if ($method->class == get_class($modelInstance) && $method->isPublic() && $method->getNumberOfParameters() == 0) {
    //             try {
    //                 $relation = $modelInstance->{$method->name}();
    //                 if ($relation instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
    //                     $relatedModel = $relation->getRelated();
    //                     $foreignKey = $relation->getForeignKeyName();
    //                     $relationsData[$foreignKey] = $relatedModel::all();
    //                 }
    //             } catch (\Exception $e) {
    //                 // Skip methods that are not relations
    //             }
    //         }
    //     }
    //     return $relationsData;
    // }
}