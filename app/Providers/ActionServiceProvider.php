<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Event;
use \App\Models\Action;
use \App\Nodels\ActionField;
use \App\Models\ActionCondition;
use \App\Models\ActionNotification;
use \App\Notifications\TemporaryNotifiable;
use \App\Notifications\SimpleEmailNotification;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Register model events
        Event::listen('eloquent.created: *', function ($modelName, $model) {
            $this->handleEvent('create', $model);
        });

        Event::listen('eloquent.updated: *', function ($modelName, $model) {
            $this->handleEvent('update', $model);
        });

        Event::listen('eloquent.deleted: *', function ($modelName, $model) {
            $this->handleEvent('delete', $model);
        });
    }

    private function handleEvent($eventType, $model)
    {
        $modelName = get_class($model[0]??null);

        $modelName=str_replace('App\Models\\', '', $modelName);
        // Fetch actions for this model and event type
        $actions = Action::where('model_name', $modelName)
            ->where('event_type', $eventType)
            ->get();

        foreach ($actions as $action) {
            $this->executeAction($action, $model);
        }
    }

    private function executeAction($action, $model)
    {
        switch ($action->action_type) {
            case 'update_record':
                $this->executeUpdateRecordAction($action, $model);
                break;
            case 'create_record':
                $this->executeCreateRecordAction($action, $model);
                break;
            case 'delete_record':
                $this->executeDeleteRecordAction($action, $model);
                break;
            case 'send_notification':
                $this->executeSendNotificationAction($action, $model);
                break;
        }
    }

    private function executeUpdateRecordAction($action, $model)
    {
        $targetModelClass = 'App\\Models\\' . $action->target_model;
        $targetModel = $targetModelClass::find($model->id);

        if ($targetModel) {
            foreach ($action->fields as $field) {
                $newValue = $this->getFieldValue($field, $model);
                $targetModel->{$field->field_name} = $newValue;
            }
            $targetModel->save();
        }
    }

    private function executeCreateRecordAction($action, $model)
    {
        $targetModelClass = 'App\\Models\\' . $action->target_model;
        $targetModel = new $targetModelClass();

        foreach ($action->fields as $field) {
            $newValue = $this->getFieldValue($field, $model);
            $targetModel->{$field->field_name} = $newValue;
        }
        $targetModel->save();
    }

    private function executeDeleteRecordAction($action, $model)
    {
        $targetModelClass = 'App\\Models\\' . $action->target_model;
        $targetModel = $targetModelClass::find($model->id);

        if ($targetModel) {
            $targetModel->delete();
        }
    }

    private function executeSendNotificationAction($action, $model)
    {
        // Assuming you have a method or class to handle sending notifications
        (new TemporaryNotifiable($action->notification->recipient))->notify(new SimpleEmailNotification($action->notification->message));
    }

    private function getFieldValue($field, $model)
    {
        switch ($field->value_source) {
            case 'static':
                return $field->static_value;
            case 'current_model_field':
                return $model->{$field->current_model_field};
            case 'related_model_field':
                $relatedModel = $model->{$field->related_model_relation};
                return $relatedModel ? $relatedModel->{$field->related_model_field} : null;
        }
        return null;
    }
}
