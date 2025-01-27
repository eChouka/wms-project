@extends('layouts.app')

@section('title', 'Edit ' . ucfirst($model))

@section('content')
<style>
    /* Consistent styling with create.blade.php */
    .custom-field-class {
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 10px;
        font-size: 14px;
    }
    .wrapper{
        display: block;
    }

    /* Section for related items */
    .related-items-section {
        margin-top: 20px;
        border: 1px solid #ccc;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    /* Styling for each related item */
    .related-item {
        margin-bottom: 10px;
        border-bottom: 1px dashed #ccc;
        padding-bottom: 10px;
        position: relative;
    }

    /* Remove button for related items */
    .remove-item-btn {
        position: absolute;
        top: 0;
        right: 0;
        cursor: pointer;
        color: red;
        font-weight: bold;
        font-size: 18px;
    }

    /* Submit and Back buttons */
    .btn-full {
        width: 100%;
        margin-top: 20px;
        padding: 12px;
        font-size: 16px;
        border-radius: 4px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #3498db;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }

    .btn-secondary {
        background-color: #95a5a6;
        color: white;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #7f8c8d;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <h1>Edit {{ ucfirst($model) }}</h1>
        <form action="{{ url('/model/'.$model . '/' . $modelInstance->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Loop through fields to display form inputs -->
            @foreach($fields as $field)
                @if($field != 'id' && $field != 'created_at' && $field != 'updated_at')
                    <div class="form-group">
                        <label for="{{ $field }}">
                            {{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}
                            @if(in_array($field, $requiredFields))
                                <span style="color: red;">*</span>
                            @endif
                        </label>
                        @if(array_key_exists($field, $relationsData))
                            <select name="{{ $field }}" class="form-control custom-field-class" id="{{ $field }}" @if(in_array($field, $requiredFields)) required @endif>
                                @foreach($relationsData[$field] as $related)
                                    <option value="{{ $related->id }}" {{ $related->id == $modelInstance->$field ? 'selected' : '' }}>
                                        {{ $related->name ?? $related->ref }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control custom-field-class" name="{{ $field }}" id="{{ $field }}" value="{{ $modelInstance->$field }}" @if(in_array($field, $requiredFields)) required @endif>
                        @endif
                    </div>
                @endif
            @endforeach

            <!-- Handle HasMany relations -->
            @php
                $hasManyRelations = $modelInstance->getHasManyRelations();
            @endphp

            @foreach($hasManyRelations as $relationName => $relationInstance)
                @php
                    $relatedModelInstance = $relationInstance->getRelated();
                    $relatedFields = $relatedModelInstance->getFillable();
                    if(method_exists($relatedModelInstance, 'getRelationMappings')){
                        $relatedRelationMappings = $relatedModelInstance->getRelationMappings()??[];
                    }else{
                        $relatedRelationMappings=[];
                    }
                    // Prepare related data for hidden fields
                    $relatedModelData = [];
                    foreach ($relatedRelationMappings as $relatedField => $relatedMethod) {
                        $relatedModelData[$relatedField] = $relatedModelInstance->$relatedMethod()->getRelated()::all()->map(function($item) {
                            return [
                                'id' => $item->id,
                                'text' => $item->name ?? $item->title ?? $item->ref ?? $item->id
                            ];
                        })->toArray();
                    }
                @endphp
                @if(count($relatedRelationMappings)>0)
                <div class="related-items-section">
                    <h3>Edit {{ ucfirst($relationName) }}</h3>



                    <div id="{{ $relationName }}-wrapper">
                        @foreach($modelInstance->{$relationName} as $index => $relatedItem)
                            <div class="related-item" data-index="{{ $index }}">
                                <span class="remove-item-btn" onclick="removeRelatedItem(this)">×</span>


                                @foreach($relatedFields as $relatedField)
                                    @if($relatedField != 'id' && $relatedField != 'created_at' && $relatedField != 'updated_at' && $relatedField != $modelInstance->getForeignKey())
                                        <div class="form-group">
                                            <label for="{{ $relationName }}[{{ $index }}][{{ $relatedField }}]">{{ ucfirst(str_replace('_', ' ', $relatedField)) }}:</label>
                                            @if(array_key_exists($relatedField, $relatedRelationMappings))
                                                <select name="{{ $relationName }}[{{ $index }}][{{ $relatedField }}]" id="{{ $relationName }}[{{ $index }}][{{ $relatedField }}]" class="form-control custom-field-class">
                                                    @foreach($relatedModelData[$relatedField] as $relatedRelated)
                                                        <option value="{{ $relatedRelated['id'] }}" {{ $relatedRelated['id'] == $relatedItem->$relatedField ? 'selected' : '' }}>
                                                            {{ $relatedRelated['text'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" class="form-control custom-field-class" name="{{ $relationName }}[{{ $index }}][{{ $relatedField }}]" id="{{ $relationName }}[{{ $index }}][{{ $relatedField }}]" value="{{ $relatedItem->$relatedField }}">
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach

                    </div>

                    <!-- Hidden fields for JS manipulation -->
                    <input type="hidden" id="{{ $relationName }}-fields" value='@json($relatedFields)'>
                    <input type="hidden" id="{{ $relationName }}-data" value='@json($relatedModelData)'>
                    <input type="hidden" id="{{ $relationName }}-mappings" value='@json($relatedRelationMappings)'>

                    <button type="button" class="btn btn-secondary" onclick="addRelatedItem('{{ $relationName }}')">Add Another {{ ucfirst($relationName) }}</button>
                </div>
                @endif
            @endforeach

            <button type="submit" class="btn btn-primary btn-full">Update</button>
        </form>
        <a href="{{ url('/model/'.$model) }}" class="btn btn-secondary btn-full mt-3">Back to {{ ucfirst($model) }} List</a>
    </div>
</div>

<script>
    function addRelatedItem(relationName) {
        var wrapper = document.getElementById(relationName + '-wrapper');
        var index = wrapper.querySelectorAll('.related-item').length;
        var newItem = document.createElement('div');
        newItem.classList.add('related-item');
        newItem.setAttribute('data-index', index);

        // Retrieve hidden field values
        var relatedFields = JSON.parse(document.getElementById(relationName + '-fields').value);
        var relatedModelData = JSON.parse(document.getElementById(relationName + '-data').value);
        var relatedRelationMappings = JSON.parse(document.getElementById(relationName + '-mappings').value);

        relatedFields.forEach(function(field) {
            if (field != 'id' && field != 'created_at' && field != 'updated_at' && field != '{{ $modelInstance->getForeignKey() }}') {
                var formGroup = document.createElement('div');
                formGroup.classList.add('form-group');

                var label = document.createElement('label');
                label.setAttribute('for', relationName + '[' + index + '][' + field + ']');
                label.textContent = field.replace(/_/g, ' ').charAt(0).toUpperCase() + field.slice(1) + ':';

                var input;
                if (relatedRelationMappings[field]) {
                    input = document.createElement('select');
                    input.setAttribute('class', 'form-control custom-field-class');
                    input.setAttribute('name', relationName + '[' + index + '][' + field + ']');
                    input.setAttribute('id', relationName + '[' + index + '][' + field + ']');

                    var options = relatedModelData[field] || [];

                    options.forEach(function(option) {
                        var opt = document.createElement('option');
                        opt.value = option.id;
                        opt.text = option.text;
                        input.appendChild(opt);
                    });
                } else {
                    input = document.createElement('input');
                    input.setAttribute('type', 'text');
                    input.setAttribute('class', 'form-control custom-field-class');
                    input.setAttribute('name', relationName + '[' + index + '][' + field + ']');
                    input.setAttribute('id', relationName + '[' + index + '][' + field + ']');
                }

                formGroup.appendChild(label);
                formGroup.appendChild(input);
                newItem.appendChild(formGroup);
            }
        });

        var removeBtn = document.createElement('span');
        removeBtn.classList.add('remove-item-btn');
        removeBtn.setAttribute('onclick', 'removeRelatedItem(this)');
        removeBtn.textContent = '×';
        newItem.appendChild(removeBtn);

        wrapper.appendChild(newItem);
    }

    function removeRelatedItem(button) {
        var item = button.closest('.related-item');
        item.remove();
    }
</script>

@endsection
