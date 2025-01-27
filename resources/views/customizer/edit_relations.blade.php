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
        <div class="tab-button active" data-tab="relationships"><a href="/customizer/edit-relations?model_name={{ $model }}">Relationships</a></div>
        <div class="tab-button" data-tab="events"><a href="/customizer/edit-events?model_name={{ $model }}">Events</a></div>
        <div class="tab-button" data-tab="actions"><a href="/customizer/edit-actions?model_name={{ $model }}">Actions</a></div>
        <div class="tab-button" data-tab="pages"><a href="/customizer/edit-pages?model_name={{ $model }}">Pages</a></div>
    </div>

    <form action="{{ url('/customizer/edit-relations') }}" method="POST">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @csrf
        <input type="hidden" name="model_name" value="{{ $model }}">


        <!-- Relationships Tab -->
        <div id="relationships" class="tab-content active">
            <h2>Relationships</h2>
            <div id="relationship-list">
                @foreach($relationships as $index => $relationship)
                <div class="form-group">
                    <label for="relationship_name_{{ $index }}">Relationship Name:</label>
                    <input type="text" name="relationships[{{ $index }}][name]" value="{{ $relationship['name'] }}" required>

                    <label for="relationship_type_{{ $index }}">Type:</label>
                    <select name="relationships[{{ $index }}][type]" required>
                        <option value="hasOne" {{ $relationship['type'] == 'HasOne' ? 'selected' : '' }}>Has One</option>
                        <option value="hasMany" {{ $relationship['type'] == 'HasMany' ? 'selected' : '' }}>Has Many</option>
                        <option value="belongsTo" {{ $relationship['type'] == 'BelongsTo' ? 'selected' : '' }}>Belongs To</option>
                        <option value="belongsToMany" {{ $relationship['type'] == 'BelongsToMany' ? 'selected' : '' }}>Belongs To Many</option>
                    </select>

                    <label for="related_model_{{ $index }}">Related Model:</label>
                    <select name="relationships[{{ $index }}][related_model]" required>
                        @foreach($availableModels as $availableModel)
                            <?php $checkModel = str_replace("App\\Models\\", "", $relationship['related_model']) ?>
                            <option value="{{ $availableModel }}" {{ $checkModel == $availableModel ? 'selected' : '' }}>
                                {{ $availableModel }}
                            </option>
                        @endforeach
                    </select>

                    <label for="model_key_{{ $index }}">Your Model Key:</label>
                    <input type="text" name="relationships[{{ $index }}][model_key]" value="{{ $relationship['model_key'] }}" required>

                    <label for="related_model_key_{{ $index }}">Related Model Key:</label>
                    <input type="text" name="relationships[{{ $index }}][related_model_key]" value="{{ $relationship['related_model_key'] }}" required>

                    <button type="button" onclick="removeRelationship(this)">Delete Relationship</button>
                </div>
                @endforeach

            </div>
            <button type="button" class="add-button" onclick="addRelationship()">Add Relationship</button>
            <hr></hr>
            <div id="mapping-list">
            @if(isset($relationMappingConfig['sourceModel']))
                <div class="form-group">
                    <h2>Relation Mapping Config</h2>
                    <div id="relation-mapping-config">
                        <h3>Source Model</h3>
                        <div id="source-model-list">
                            @foreach($relationMappingConfig['sourceModel'] as $index => $sourceModel)
                            <div class="form-group">
                                <label for="source_model_name_{{ $index }}">Source Model Name:</label>
                                <input type="text" name="relationMappingConfig[sourceModel][{{ $index }}][model_name]" value="{{ $index }}" required>

                                <label for="source_model_target_field_{{ $index }}">Target Field:</label>
                                <input type="text" name="relationMappingConfig[sourceModel][{{ $index }}][target_field]" value="{{ $sourceModel['target_field'] }}" required>

                                <label for="source_model_local_field_{{ $index }}">Local Field:</label>
                                <input type="text" name="relationMappingConfig[sourceModel][{{ $index }}][local_field]" value="{{ $sourceModel['local_field'] }}" required>

                                <button type="button" onclick="removeSourceModel(this)">Delete Source Model</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-button" onclick="addSourceModel()">Add Source Model</button>

                        <h3>Target Model</h3>
                        <div id="target-model-list">
                            @foreach($relationMappingConfig['targetModel'] as $index => $targetModel)
                            <div class="form-group">
                                <label for="target_model_name_{{ $index }}">Target Model Name:</label>
                                <input type="text" name="relationMappingConfig[targetModel][{{ $index }}][model_name]" value="{{ $index }}" required>

                                <label for="target_model_target_field_{{ $index }}">Target Field:</label>
                                <input type="text" name="relationMappingConfig[targetModel][{{ $index }}][target_field]" value="{{ $targetModel['target_field'] }}" required>

                                <label for="target_model_local_field_{{ $index }}">Local Field:</label>
                                <input type="text" name="relationMappingConfig[targetModel][{{ $index }}][local_field]" value="{{ $targetModel['local_field'] }}" required>

                                <button type="button" onclick="removeTargetModel(this)">Delete Target Model</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-button" onclick="addTargetModel()">Add Target Model</button>

                        <h3>Field Mapping</h3>
                        <div id="field-mapping-list">
                            @foreach($relationMappingConfig['fieldMapping'] as $sourceField => $targetField)
                            <div class="form-group inline-group">
                                <label for="field_mapping_source_{{ $loop->index }}">Source Field:</label>
                                <input type="text" name="relationMappingConfig[fieldMapping][{{ $loop->index }}][source_field]" value="{{ $sourceField }}" required>

                                <label for="field_mapping_target_{{ $loop->index }}">Target Field:</label>
                                <input type="text" name="relationMappingConfig[fieldMapping][{{ $loop->index }}][target_field]" value="{{ $targetField }}" required>

                                <button type="button" onclick="removeFieldMapping(this)">Delete Field Mapping</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-button" onclick="addFieldMapping()">Add Field Mapping</button>
                    </div>
                </div>
                @endif
        </div>
        </div>




        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Save Model</button>
    </form>
</div>

<script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            const tabContent = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab-button');

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContent.forEach(content => content.classList.remove('active'));

            button.classList.add('active');
            document.getElementById(button.dataset.tab).classList.add('active');
        });
    });

    function addRelationship() {
        const relationshipList = document.getElementById('relationship-list');
        const index = relationshipList.children.length;
        const relationshipHtml = `
            <div class="form-group">
                <label for="relationship_name_${index}">Relationship Name:</label>
                <input type="text" name="relationships[${index}][name]" required>

                <label for="relationship_type_${index}">Type:</label>
                <select name="relationships[${index}][type]" required>
                    <option value="hasOne">Has One</option>
                    <option value="hasMany">Has Many</option>
                    <option value="belongsTo">Belongs To</option>
                    <option value="belongsToMany">Belongs To Many</option>
                </select>

                <label for="related_model_${index}">Related Model:</label>
                <select name="relationships[${index}][related_model]" required>
                    @foreach($availableModels as $availableModel)
                        <option value="{{ $availableModel }}">{{ $availableModel }}</option>
                    @endforeach
                </select>

                <label for="model_key_${index}">Your Model Key:</label>
                <input type="text" name="relationships[${index}][model_key]" required>

                <label for="related_model_key_${index}">Related Model Key:</label>
                <input type="text" name="relationships[${index}][related_model_key]" required>

                <button type="button" onclick="removeRelationship(this)">Delete Relationship</button>
            </div>`;
        relationshipList.insertAdjacentHTML('beforeend', relationshipHtml);
    }

    function removeRelationship(button) {
        button.parentElement.remove();
    }

    function addSourceModel() {
    const sourceModelList = document.getElementById('source-model-list');
    const index = sourceModelList.children.length;
    const sourceModelHtml = `
        <div class="form-group">
            <label for="source_model_name_${index}">Source Model Name:</label>
            <input type="text" name="relationMappingConfig[sourceModel][${index}][model_name]" required>

            <label for="source_model_target_field_${index}">Target Field:</label>
            <input type="text" name="relationMappingConfig[sourceModel][${index}][target_field]" required>

            <label for="source_model_local_field_${index}">Local Field:</label>
            <input type="text" name="relationMappingConfig[sourceModel][${index}][local_field]" required>

            <button type="button" onclick="removeSourceModel(this)">Delete Source Model</button>
        </div>`;
    sourceModelList.insertAdjacentHTML('beforeend', sourceModelHtml);
}

function removeSourceModel(button) {
    button.parentElement.remove();
}

function addTargetModel() {
    const targetModelList = document.getElementById('target-model-list');
    const index = targetModelList.children.length;
    const targetModelHtml = `
        <div class="form-group">
            <label for="target_model_name_${index}">Target Model Name:</label>
            <input type="text" name="relationMappingConfig[targetModel][${index}][model_name]" required>

            <label for="target_model_target_field_${index}">Target Field:</label>
            <input type="text" name="relationMappingConfig[targetModel][${index}][target_field]" required>

            <label for="target_model_local_field_${index}">Local Field:</label>
            <input type="text" name="relationMappingConfig[targetModel][${index}][local_field]" required>

            <button type="button" onclick="removeTargetModel(this)">Delete Target Model</button>
        </div>`;
    targetModelList.insertAdjacentHTML('beforeend', targetModelHtml);
}

function removeTargetModel(button) {
    button.parentElement.remove();
}

function addFieldMapping() {
    const fieldMappingList = document.getElementById('field-mapping-list');
    const index = fieldMappingList.children.length;
    const fieldMappingHtml = `
        <div class="form-group inline-group">
            <label for="field_mapping_source_${index}">Source Field:</label>
            <input type="text" name="relationMappingConfig[fieldMapping][${index}][source_field]" required>

            <label for="field_mapping_target_${index}">Target Field:</label>
            <input type="text" name="relationMappingConfig[fieldMapping][${index}][target_field]" required>

            <button type="button" onclick="removeFieldMapping(this)">Delete Field Mapping</button>
        </div>`;
    fieldMappingList.insertAdjacentHTML('beforeend', fieldMappingHtml);
}

function removeFieldMapping(button) {
    button.parentElement.remove();
}


</script>
@endsection
