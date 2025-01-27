@extends('layouts.app')

@section('title', 'Edit Model')

@section('content')
<style>
    .tab-container {
        display: flex;
        flex-direction: column;
        max-width: 1000px;
        margin: 0 auto;
    }

    .tabs {
        display: flex;
        border-bottom: 1px solid #ccc;
        margin-bottom: 20px;
    }

    .tab-button {
        padding: 10px 20px;
        cursor: pointer;
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        transition: background-color 0.3s ease;
    }

    .tab-button.active {
        background-color: #3498db;
        color: white;
    }

    .tab-content {
        display: none;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 0 0 8px 8px;
        background-color: #fff;
    }

    .tab-content.active {
        display: block;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 8px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }

    .form-group button {
        display: block;
        padding: 10px 20px;
        margin-top: 10px;
        background-color: #e74c3c;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-group button:hover {
        background-color: #c0392b;
    }

    .add-button {
        background-color: #3498db;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }

    .add-button:hover {
        background-color: #2980b9;
    }

    .action-container {
        margin-bottom: 20px;
    }

    .form-group.inline-group {
        display: flex;
        align-items: center;
    }

    .form-group.inline-group select, .form-group.inline-group input {
        flex: 1;
        margin-right: 10px;
    }

    .form-group.inline-group input[type="radio"] {
        margin-right: 5px;
    }
    .wrapper{
        display: block;
    }
    .active a{
        color:#fff;
    }
    .tab-button{
        position: relative;
        min-height: 45px;
        min-width: 140px;
    }

    .tab-button a {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      color: inherit; /* Inherit color from parent */
      background: transparent; /* Ensure background is transparent */
      z-index: 1; /* Ensure the link is above other elements */
    }
</style>
<div class="container tab-container">
    <h1>Edit Model: {{ $model }}</h1>

    <div class="tabs">
        <div class="tab-button" data-tab="fields"><a href="/customizer/edit-fields?model_name={{ $model }}">Fields</a></div>
        <div class="tab-button" data-tab="relationships"><a href="/customizer/edit-relations?model_name={{ $model }}">Relationships</a></div>
        <div class="tab-button" data-tab="events"><a href="/customizer/edit-events?model_name={{ $model }}">Events</a></div>
        <div class="tab-button active" data-tab="actions"><a href="/customizer/edit-actions?model_name={{ $model }}">Actions</a></div>
        <div class="tab-button" data-tab="pages"><a href="/customizer/edit-pages?model_name={{ $model }}">Pages</a></div>
    </div>

    <form action="{{ url('/customizer/edit-actions') }}" method="POST">
        @csrf
        <input type="hidden" name="model_name" value="{{ $model }}">

        <!-- Actions Tab -->
        <div id="actions" class="tab-content active">
            <h2>Actions Configuration</h2>

            <!-- Existing Action Configurations -->
            <div id="action-list">
                @foreach($actions as $actionIndex => $action)
                <div class="form-group action-group" id="action_div_{{ $actionIndex }}">
                    <label for="event_type_{{ $actionIndex }}">Event Type:</label>
                    <input type="hidden" name="actions[{{ $actionIndex }}][id]" value="{{ $action->id }}" />
                    <select name="actions[{{ $actionIndex }}][event_type]" required>
                        <option value="create" {{ $action->event_type == 'create' ? 'selected' : '' }}>On Create</option>
                        <option value="update" {{ $action->event_type == 'update' ? 'selected' : '' }}>On Update</option>
                        <option value="delete" {{ $action->event_type == 'delete' ? 'selected' : '' }}>On Delete</option>
                    </select>

                    <label for="action_type_{{ $actionIndex }}">Action Type:</label>
                    <select name="actions[{{ $actionIndex }}][action_type]" required>
                        <option value="update_record" {{ $action->action_type == 'update_record' ? 'selected' : '' }}>Update Record</option>
                        <option value="create_record" {{ $action->action_type == 'create_record' ? 'selected' : '' }}>Create a New Record</option>
                        <option value="delete_record" {{ $action->action_type == 'delete_record' ? 'selected' : '' }}>Delete a Record</option>
                        <option value="send_notification" {{ $action->action_type == 'send_notification' ? 'selected' : '' }}>Send Notification</option>
                    </select>

                    <!-- Conditional Logic for Action Type: Update Record -->
                    <div class="action-configuration" data-action="update_record" style="display: {{ $action->action_type == 'update_record' ? 'block' : 'none' }};">
                        <label for="update_model_{{ $actionIndex }}">Model to Update:</label>
                        <select name="actions[{{ $actionIndex }}][update_model]">
                            @foreach($availableModels as $availableModel)
                                <option value="{{ $availableModel }}" {{ $action->target_model == $availableModel ? 'selected' : '' }}>{{ $availableModel }}</option>
                            @endforeach
                        </select>

                        <!-- Updating Multiple Fields -->
                        <div class="update-fields-list" data-index="{{ $actionIndex }}">
                            <h5>Fields to Update</h5>
                            @foreach($action->fields as $fieldIndex => $field)
                            <div class="update-field-group">
                                <input type="hidden" name="actions[{{ $actionIndex }}][update_fields][{{ $fieldIndex }}][id]" value="{{ $field->id }}" />

                                <label for="update_field_{{ $actionIndex }}_{{ $fieldIndex }}">Field:</label>
                                <input type="text" name="actions[{{ $actionIndex }}][update_fields][{{ $fieldIndex }}][field]" value="{{ $field->field_name }}" >

                                <label for="update_value_{{ $actionIndex }}_{{ $fieldIndex }}">New Value:</label>
                                <input type="text" name="actions[{{ $actionIndex }}][update_fields][{{ $fieldIndex }}][value]" value="{{ $field->static_value ?? $field->current_model_field ?? $field->related_model_field }}" >

                                <label for="value_source_{{ $actionIndex }}_{{ $fieldIndex }}">Value Source:</label>
                                <select name="actions[{{ $actionIndex }}][update_fields][{{ $fieldIndex }}][value_source]" class="value-source" data-index="{{ $actionIndex }}_{{ $fieldIndex }}" >
                                    <option value="static" {{ $field->value_source == 'static' ? 'selected' : '' }}>Static Value</option>
                                    <option value="current_model_field" {{ $field->value_source == 'current_model_field' ? 'selected' : '' }}>Current Model Field</option>
                                    <option value="related_model_field" {{ $field->value_source == 'related_model_field' ? 'selected' : '' }}>Related Model Field</option>
                                </select>

                                <!-- Additional Fields for Related Model Field -->
                                <div class="related-model-config" style="display: {{ $field->value_source == 'related_model_field' ? 'block' : 'none' }};">
                                    <label for="related_model_relation_{{ $actionIndex }}_{{ $fieldIndex }}">Relation:</label>
                                    <select name="actions[{{ $actionIndex }}][update_fields][{{ $fieldIndex }}][related_model_relation]" class="related-model-relation" data-index="{{ $actionIndex }}_{{ $fieldIndex }}">
                                        @foreach($relationships as $relationship)
                                            <option value="{{ $relationship['name'] }}" {{ $field->related_model_relation == $relationship['name'] ? 'selected' : '' }}>{{ $relationship['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-button" onclick="addUpdateField('{{ $actionIndex }}')">Add Field Mapping</button>
                    </div>

                    <!-- Conditional Logic for Action Type: Create Record -->
                    <div class="action-configuration" data-action="create_record" style="display: {{ $action->action_type == 'create_record' ? 'block' : 'none' }};">
                        <label for="create_model_{{ $actionIndex }}">Model to Create:</label>
                        <select name="actions[{{ $actionIndex }}][create_model]">
                            @foreach($availableModels as $availableModel)
                                <option value="{{ $availableModel }}" {{ $action->target_model == $availableModel ? 'selected' : '' }}>{{ $availableModel }}</option>
                            @endforeach
                        </select>

                        <!-- Creating Multiple Fields -->
                        <div class="create-fields-list" data-index="{{ $actionIndex }}">
                            <h5>Fields to Populate</h5>
                            @foreach($action->fields as $fieldIndex => $field)
                            <div class="create-field-group">
                                <input type="hidden" name="actions[{{ $actionIndex }}][create_fields][{{ $fieldIndex }}][id]" value="{{ $field->id }}" />

                                <label for="create_field_{{ $actionIndex }}_{{ $fieldIndex }}">Field:</label>
                                <input type="text" name="actions[{{ $actionIndex }}][create_fields][{{ $fieldIndex }}][field]" value="{{ $field->field_name }}" >

                                <label for="create_value_{{ $actionIndex }}_{{ $fieldIndex }}">Value:</label>
                                <input type="text" name="actions[{{ $actionIndex }}][create_fields][{{ $fieldIndex }}][value]" value="{{ $field->static_value ?? $field->current_model_field ?? $field->related_model_field }}" >

                                <label for="create_value_source_{{ $actionIndex }}_{{ $fieldIndex }}">Value Source:</label>
                                <select name="actions[{{ $actionIndex }}][create_fields][{{ $fieldIndex }}][value_source]" class="value-source" data-index="{{ $actionIndex }}_{{ $fieldIndex }}" >
                                    <option value="static" {{ $field->value_source == 'static' ? 'selected' : '' }}>Static Value</option>
                                    <option value="current_model_field" {{ $field->value_source == 'current_model_field' ? 'selected' : '' }}>Current Model Field</option>
                                    <option value="related_model_field" {{ $field->value_source == 'related_model_field' ? 'selected' : '' }}>Related Model Field</option>
                                </select>

                                <!-- Additional Fields for Related Model Field -->
                                <div class="related-model-config" style="display: {{ $field->value_source == 'related_model_field' ? 'block' : 'none' }};">
                                    <label for="related_model_relation_{{ $actionIndex }}_{{ $fieldIndex }}">Relation:</label>
                                    <select name="actions[{{ $actionIndex }}][create_fields][{{ $fieldIndex }}][related_model_relation]" class="related-model-relation" data-index="{{ $actionIndex }}_{{ $fieldIndex }}">
                                        @foreach($relationships as $relationship)
                                            <option value="{{ $relationship['name'] }}" {{ $field->related_model_relation == $relationship['name'] ? 'selected' : '' }}>{{ $relationship['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-button" onclick="addCreateField('{{ $actionIndex }}')">Add Field Mapping</button>
                    </div>

                    <!-- Conditional Logic for Action Type: Delete Record -->
                    <div class="action-configuration" data-action="delete_record" style="display: {{ $action->action_type == 'delete_record' ? 'block' : 'none' }};">
                        <label for="delete_model_{{ $actionIndex }}">Model to Delete:</label>
                        <select name="actions[{{ $actionIndex }}][delete_model]">
                            @foreach($availableModels as $availableModel)
                                <option value="{{ $availableModel }}" {{ $action->target_model == $availableModel ? 'selected' : '' }}>{{ $availableModel }}</option>
                            @endforeach
                        </select>

                        <h5>Conditions to Identify Record(s)</h5>
                        <div class="delete-conditions-list" data-index="{{ $actionIndex }}">
                            @foreach($action->conditions??[] as $conditionIndex => $condition)
                            <div class="delete-condition-group">
                                <label for="delete_condition_{{ $actionIndex }}_{{ $conditionIndex }}">Condition Field:</label>
                                <input type="text" name="actions[{{ $actionIndex }}][delete_conditions][{{ $conditionIndex }}][field]" value="{{ $condition->field_name }}" placeholder="Field Name" >

                                <label for="delete_condition_value_{{ $actionIndex }}_{{ $conditionIndex }}">Condition Value:</label>
                                <input type="text" name="actions[{{ $actionIndex }}][delete_conditions][{{ $conditionIndex }}][value]" value="{{ $condition->value }}" placeholder="Value" >

                                <label for="delete_condition_operator_{{ $actionIndex }}_{{ $conditionIndex }}">Operator:</label>
                                <select name="actions[{{ $actionIndex }}][delete_conditions][{{ $conditionIndex }}][operator]" >
                                    <option value="=" {{ $condition->operator == '=' ? 'selected' : '' }}>=</option>
                                    <option value="!=" {{ $condition->operator == '!=' ? 'selected' : '' }}>!=</option>
                                    <option value=">" {{ $condition->operator == '>' ? 'selected' : '' }}>></option>
                                    <option value="<" {{ $condition->operator == '<' ? 'selected' : '' }}><</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-button" onclick="addDeleteCondition('{{ $actionIndex }}')">Add Condition</button>
                    </div>

                    <!-- Conditional Logic for Action Type: Send Notification -->
                    <div class="action-configuration" data-action="send_notification" style="display: {{ $action->action_type == 'send_notification' ? 'block' : 'none' }};">
                        <h5>Notification Details</h5>
                        <div class="notification-group">
                            <label for="notification_recipient_{{ $actionIndex }}">Recipient:</label>
                            <input type="text" name="actions[{{ $actionIndex }}][notification][recipient]" value="{{ $action->notification->recipient ?? '' }}" placeholder="Email/Username" >

                            <label for="notification_subject_{{ $actionIndex }}">Subject:</label>
                            <input type="text" name="actions[{{ $actionIndex }}][notification][subject]" value="{{ $action->notification->subject ?? '' }}" placeholder="Notification Subject" >

                            <label for="notification_message_{{ $actionIndex }}">Message:</label>
                            <textarea name="actions[{{ $actionIndex }}][notification][message]" placeholder="Notification Message" >{{ $action->notification->message ?? '' }}</textarea>
                        </div>
                    </div>

                    <button type="button" onclick="removeAction(this)">Delete Action</button>
                </div>
                @endforeach
            </div>
            <button type="button" class="add-button" onclick="addAction()">Add Action</button>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Save Configuration</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('select[name^="actions["][name$="[action_type]"]').forEach(select => {
            select.addEventListener('change', function() {
                const actionType = this.value;
                const actionConfigDiv = this.closest('.action-group').querySelector('.action-configuration[data-action="'+actionType+'"]');
                this.closest('.action-group').querySelectorAll('.action-configuration').forEach(div => div.style.display = 'none');
                if (actionConfigDiv) {
                    actionConfigDiv.style.display = 'block';
                }
            });
        });

        document.querySelectorAll('.value-source').forEach(select => {
            select.addEventListener('change', function() {
                const valueSource = this.value;
                const relatedModelConfig = this.closest('.update-field-group, .create-field-group').querySelector('.related-model-config');
                if (valueSource === 'related_model_field') {
                    relatedModelConfig.style.display = 'block';
                } else {
                    relatedModelConfig.style.display = 'none';
                }
            });
        });

        window.addUpdateField = function(index) {
            const updateFieldsList = document.querySelector(`.update-fields-list[data-index="${index}"]`);
            const fieldIndex = updateFieldsList.children.length;
            const updateFieldHtml = `
                <div class="update-field-group">
                    <label for="update_field_${index}_${fieldIndex}">Field:</label>
                    <input type="text" name="actions[${index}][update_fields][${fieldIndex}][field]" placeholder="Field Name" >

                    <label for="update_value_${index}_${fieldIndex}">New Value:</label>
                    <input type="text" name="actions[${index}][update_fields][${fieldIndex}][value]" placeholder="Value" >

                    <label for="value_source_${index}_${fieldIndex}">Value Source:</label>
                    <select name="actions[${index}][update_fields][${fieldIndex}][value_source]" class="value-source" data-index="${index}_${fieldIndex}" >
                        <option value="static">Static Value</option>
                        <option value="current_model_field">Current Model Field</option>
                        <option value="related_model_field">Related Model Field</option>
                    </select>

                    <!-- Additional Fields for Related Model Field -->
                    <div class="related-model-config" style="display: none;">
                        <label for="related_model_relation_${index}_${fieldIndex}">Relation:</label>
                        <select name="actions[${index}][update_fields][${fieldIndex}][related_model_relation]" class="related-model-relation" data-index="${index}_${fieldIndex}">
                            @foreach($relationships as $relationship)
                                <option value="{{ $relationship['name'] }}">{{ $relationship['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>`;
            updateFieldsList.insertAdjacentHTML('beforeend', updateFieldHtml);

            // Re-attach event listeners to new elements
            attachEventListeners();
        };

        window.addCreateField = function(index) {
            const createFieldsList = document.querySelector(`.create-fields-list[data-index="${index}"]`);
            const fieldIndex = createFieldsList.children.length;
            const createFieldHtml = `
                <div class="create-field-group">
                    <label for="create_field_${index}_${fieldIndex}">Field:</label>
                    <input type="text" name="actions[${index}][create_fields][${fieldIndex}][field]" placeholder="Field Name" >

                    <label for="create_value_${index}_${fieldIndex}">Value:</label>
                    <input type="text" name="actions[${index}][create_fields][${fieldIndex}][value]" placeholder="Value" >

                    <label for="create_value_source_${index}_${fieldIndex}">Value Source:</label>
                    <select name="actions[${index}][create_fields][${fieldIndex}][value_source]" class="value-source" data-index="${index}_${fieldIndex}" >
                        <option value="static">Static Value</option>
                        <option value="current_model_field">Current Model Field</option>
                        <option value="related_model_field">Related Model Field</option>
                    </select>

                    <!-- Additional Fields for Related Model Field -->
                    <div class="related-model-config" style="display: none;">
                        <label for="related_model_relation_${index}_${fieldIndex}">Relation:</label>
                        <select name="actions[${index}][create_fields][${fieldIndex}][related_model_relation]" class="related-model-relation" data-index="${index}_${fieldIndex}">
                            @foreach($relationships as $relationship)
                                <option value="{{ $relationship['name'] }}">{{ $relationship['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>`;
            createFieldsList.insertAdjacentHTML('beforeend', createFieldHtml);

            // Re-attach event listeners to new elements
            attachEventListeners();
        };

        window.addDeleteCondition = function(index) {
            const deleteConditionsList = document.querySelector(`.delete-conditions-list[data-index="${index}"]`);
            const conditionIndex = deleteConditionsList.children.length;
            const deleteConditionHtml = `
                <div class="delete-condition-group">
                    <label for="delete_condition_${index}_${conditionIndex}">Condition Field:</label>
                    <input type="text" name="actions[${index}][delete_conditions][${conditionIndex}][field]" placeholder="Field Name" >

                    <label for="delete_condition_value_${index}_${conditionIndex}">Condition Value:</label>
                    <input type="text" name="actions[${index}][delete_conditions][${conditionIndex}][value]" placeholder="Value" >

                    <label for="delete_condition_operator_${index}_${conditionIndex}">Operator:</label>
                    <select name="actions[${index}][delete_conditions][${conditionIndex}][operator]" >
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value=">">></option>
                        <option value="<"><</option>
                    </select>
                </div>`;
            deleteConditionsList.insertAdjacentHTML('beforeend', deleteConditionHtml);
        };

        window.addAction = function() {
            const actionList = document.getElementById('action-list');
            const actionIndex = actionList.children.length;

            const actionHtml = `
                <div class="form-group action-group">
                    <label for="event_type_${actionIndex}">Event Type:</label>
                    <select name="actions[${actionIndex}][event_type]" required>
                        <option value="create">On Create</option>
                        <option value="update">On Update</option>
                        <option value="delete">On Delete</option>
                    </select>

                    <label for="action_type_${actionIndex}">Action Type:</label>
                    <select name="actions[${actionIndex}][action_type]" required>
                        <option value="update_record">Update Record</option>
                        <option value="create_record">Create a New Record</option>
                        <option value="delete_record">Delete a Record</option>
                        <option value="send_notification">Send Notification</option>
                    </select>

                    <div class="action-configuration" data-action="update_record" style="display: none;">
                        <label for="update_model_${actionIndex}">Model to Update:</label>
                        <select name="actions[${actionIndex}][update_model]">
                            @foreach($availableModels as $availableModel)
                                <option value="{{ $availableModel }}">{{ $availableModel }}</option>
                            @endforeach
                        </select>

                        <div class="update-fields-list" data-index="${actionIndex}">
                            <h5>Fields to Update</h5>
                            <div class="update-field-group">
                                <label for="update_field_${actionIndex}_0">Field:</label>
                                <input type="text" name="actions[${actionIndex}][update_fields][0][field]" placeholder="Field Name" >

                                <label for="update_value_${actionIndex}_0">New Value:</label>
                                <input type="text" name="actions[${actionIndex}][update_fields][0][value]" placeholder="Value" >

                                <label for="value_source_${actionIndex}_0">Value Source:</label>
                                <select name="actions[${actionIndex}][update_fields][0][value_source]" class="value-source" data-index="${actionIndex}_0">
                                    <option value="static">Static Value</option>
                                    <option value="current_model_field">Current Model Field</option>
                                    <option value="related_model_field">Related Model Field</option>
                                </select>

                                <div class="related-model-config" style="display: none;">
                                    <label for="related_model_relation_${actionIndex}_0">Relation:</label>
                                    <select name="actions[${actionIndex}][update_fields][0][related_model_relation]" class="related-model-relation" data-index="${actionIndex}_0">
                                        @foreach($relationships as $relationship)
                                            <option value="{{ $relationship['name'] }}">{{ $relationship['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="add-button" onclick="addUpdateField('${actionIndex}')">Add Field Mapping</button>
                    </div>

                    <div class="action-configuration" data-action="create_record" style="display: none;">
                        <label for="create_model_${actionIndex}">Model to Create:</label>
                        <select name="actions[${actionIndex}][create_model]">
                            @foreach($availableModels as $availableModel)
                                <option value="{{ $availableModel }}">{{ $availableModel }}</option>
                            @endforeach
                        </select>

                        <div class="create-fields-list" data-index="${actionIndex}">
                            <h5>Fields to Populate</h5>
                            <div class="create-field-group">
                                <label for="create_field_${actionIndex}_0">Field:</label>
                                <input type="text" name="actions[${actionIndex}][create_fields][0][field]" placeholder="Field Name" >

                                <label for="create_value_${actionIndex}_0">Value:</label>
                                <input type="text" name="actions[${actionIndex}][create_fields][0][value]" placeholder="Value" >

                                <label for="create_value_source_${actionIndex}_0">Value Source:</label>
                                <select name="actions[${actionIndex}][create_fields][0][value_source]" class="value-source" data-index="${actionIndex}_0">
                                    <option value="static">Static Value</option>
                                    <option value="current_model_field">Current Model Field</option>
                                    <option value="related_model_field">Related Model Field</option>
                                </select>

                                <div class="related-model-config" style="display: none;">
                                    <label for="related_model_relation_${actionIndex}_0">Relation:</label>
                                    <select name="actions[${actionIndex}][create_fields][0][related_model_relation]" class="related-model-relation" data-index="${actionIndex}_0">
                                        @foreach($relationships as $relationship)
                                            <option value="{{ $relationship['name'] }}">{{ $relationship['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="add-button" onclick="addCreateField('${actionIndex}')">Add Field Mapping</button>
                    </div>

                    <div class="action-configuration" data-action="delete_record" style="display: none;">
                        <label for="delete_model_${actionIndex}">Model to Delete:</label>
                        <select name="actions[${actionIndex}][delete_model]">
                            @foreach($availableModels as $availableModel)
                                <option value="{{ $availableModel }}">{{ $availableModel }}</option>
                            @endforeach
                        </select>

                        <h5>Conditions to Identify Record(s)</h5>
                        <div class="delete-conditions-list" data-index="${actionIndex}">
                            <div class="delete-condition-group">
                                <label for="delete_condition_${actionIndex}_0">Condition Field:</label>
                                <input type="text" name="actions[${actionIndex}][delete_conditions][0][field]" placeholder="Field Name" >

                                <label for="delete_condition_value_${actionIndex}_0">Condition Value:</label>
                                <input type="text" name="actions[${actionIndex}][delete_conditions][0][value]" placeholder="Value" >

                                <label for="delete_condition_operator_${actionIndex}_0">Operator:</label>
                                <select name="actions[${actionIndex}][delete_conditions][0][operator]" >
                                    <option value="=">=</option>
                                    <option value="!=">!=</option>
                                    <option value=">">></option>
                                    <option value="<"><</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="add-button" onclick="addDeleteCondition('${actionIndex}')">Add Condition</button>
                    </div>

                    <div class="action-configuration" data-action="send_notification" style="display: none;">
                        <h5>Notification Details</h5>
                        <div class="notification-group">
                            <label for="notification_recipient_${actionIndex}">Recipient:</label>
                            <input type="text" name="actions[${actionIndex}][notification][recipient]" placeholder="Email/Username" >

                            <label for="notification_subject_${actionIndex}">Subject:</label>
                            <input type="text" name="actions[${actionIndex}][notification][subject]" placeholder="Notification Subject" >

                            <label for="notification_message_${actionIndex}">Message:</label>
                            <textarea name="actions[${actionIndex}][notification][message]" placeholder="Notification Message" ></textarea>
                        </div>
                    </div>

                    <button type="button" onclick="removeAction(this)">Delete Action</button>
                </div>
            `;

            actionList.insertAdjacentHTML('beforeend', actionHtml);

            // Re-attach event listeners to the new elements
            attachEventListeners();
        };

        function attachEventListeners() {
            document.querySelectorAll('select[name^="actions["][name$="[action_type]"]').forEach(select => {
                select.addEventListener('change', function() {
                    const actionType = this.value;
                    const actionConfigDiv = this.closest('.action-group').querySelector('.action-configuration[data-action="'+actionType+'"]');
                    this.closest('.action-group').querySelectorAll('.action-configuration').forEach(div => div.style.display = 'none');
                    if (actionConfigDiv) {
                        actionConfigDiv.style.display = 'block';
                    }
                });
            });

            document.querySelectorAll('.value-source').forEach(select => {
                select.addEventListener('change', function() {
                    const valueSource = this.value;
                    const relatedModelConfig = this.closest('.update-field-group, .create-field-group').querySelector('.related-model-config');
                    if (valueSource === 'related_model_field') {
                        relatedModelConfig.style.display = 'block';
                    } else {
                        relatedModelConfig.style.display = 'none';
                    }
                });
            });
        }

        window.removeAction = function(button) {
            button.closest('.form-group').remove();
        };
    });
</script>
@endsection
