@extends('layouts.app')

@section('content')
<style>
    /* Container and Header Styles */

    .wrapper{
        display: block;
    }
    h1 {
        font-size: 28px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Form Group Styles */
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: 600;
        color: #34495e;
        margin-bottom: 5px;
        display: block;
    }
    .form-control {
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form{
        padding: 30px;
        border-radius: 7px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
    }

    /* Related Items Section */
    .related-items-section {
        margin-top: 30px;
        border: 1px solid #ddd;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .related-items-section h3 {
        font-size: 22px;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .related-item {
        margin-bottom: 20px;
        border-bottom: 1px dashed #ccc;
        padding-bottom: 10px;
        position: relative;
    }
    .related-item:last-child {
        border-bottom: none;
    }
    .remove-item-btn {
        position: absolute;
        top: 0;
        right: 0;
        cursor: pointer;
        color: red;
        font-weight: bold;
        background: transparent;
        border: none;
    }
    .remove-item-btn:hover {
        color: #c0392b;
    }

    /* Button Styles */
    .btn-primary {
        background-color: #3498db;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-top: 20px;
    }
    .btn-primary:hover {
        background-color: #2980b9;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary {
        background-color: #7f8c8d;
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 10px;
        display: block;
        text-align: center;
    }
    .btn-secondary:hover {
        background-color: #95a5a6;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <h1>Create New {{ ucfirst($model) }}</h1>
        <form action="{{ url('/model/'.$model) }}" class="form" method="POST">

            @csrf
            @foreach($fields as $field)
                @if($field != 'id' && $field != 'created_at' && $field != 'updated_at')
                    <div class="form-group">
                        <label for="{{ $field }}">{{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}
                            @if(in_array($field, $requiredFields))
                                <span style="color: red;">*</span>
                            @endif
                        </label>
                        @if(array_key_exists($field, $relationsData))
                            <select name="{{ $field }}" id="{{ $field }}" @if(in_array($field, $requiredFields)) required @endif class="form-control custom-field-class">
                                @foreach($relationsData[$field] as $related)
                                    <option value="{{ $related->id }}">{{ $related->name ?? $related->ref }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control custom-field-class" @if(in_array($field, $requiredFields)) required @endif name="{{ $field }}" id="{{ $field }}">
                        @endif
                    </div>
                @endif
            @endforeach

            @php
                $hasManyRelations = $modelInstance->getHasManyRelations();
            @endphp

            @foreach($hasManyRelations as $relationName => $relationInstance)
                <div class="related-items-section">
                    <h3>Add {{ ucfirst($relationName) }}</h3>
                    <div id="{{ $relationName }}-wrapper">
                        <div class="related-item" data-index="0">
                            <span class="remove-item-btn" onclick="removeRelatedItem(this)">×</span>
                            @php
                                $relatedModelInstance = $relationInstance->getRelated();
                                $relatedFields = $relatedModelInstance->getFillable();
                                $relatedRelationMappings = $relatedModelInstance->getRelationMappings();
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

                            @foreach($relatedFields as $relatedField)
                                @if($relatedField != 'id' && $relatedField != 'created_at' && $relatedField != 'updated_at' && $relatedField != $modelInstance->getForeignKey())
                                    <div class="form-group">
                                        <label for="{{ $relationName }}[0][{{ $relatedField }}]">{{ ucfirst(str_replace('_', ' ', $relatedField)) }}</label>
                                        @if(array_key_exists($relatedField, $relatedRelationMappings))
                                            <select name="{{ $relationName }}[0][{{ $relatedField }}]" id="{{ $relationName }}[0][{{ $relatedField }}]" class="form-control custom-field-class">
                                                @foreach($relatedModelData[$relatedField] as $relatedRelated)
                                                    <option value="{{ $relatedRelated['id'] }}">{{ $relatedRelated['text'] }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control custom-field-class" name="{{ $relationName }}[0][{{ $relatedField }}]" id="{{ $relationName }}[0][{{ $relatedField }}]">
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Hidden fields to store related fields and data for JS --}}
                    <input type="hidden" id="{{ $relationName }}-fields" value='@json($relatedFields)'>
                    <input type="hidden" id="{{ $relationName }}-data" value='@json($relatedModelData)'>
                    <input type="hidden" id="{{ $relationName }}-mappings" value='@json($relatedRelationMappings)'>

                    <button type="button" class="btn btn-secondary" onclick="addRelatedItem('{{ $relationName }}')">Add Another {{ ucfirst($relationName) }}</button>
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary btn-full">Create</button>
        </form>
        <!-- <a href="{{ url('/model/'.$model) }}" class="btn btn-secondary">Back to {{ ucfirst($model) }} List</a> -->
    </div>
</div>

<script>
    function addRelatedItem(relationName) {
        var wrapper = document.getElementById(relationName + '-wrapper');
        var index = wrapper.querySelectorAll('.related-item').length;
        var newItem = document.createElement('div');
        newItem.classList.add('related-item');
        newItem.setAttribute('data-index', index);

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
