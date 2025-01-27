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
    .wrapper{
        display: block;
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
        <div class="tab-button"><a href="/customizer/edit-fields?model_name={{ $model }}">Fields</a></div>
        <div class="tab-button"><a href="/customizer/edit-relations?model_name={{ $model }}">Relationships</a></div>
        <div class="tab-button active"><a href="/customizer/edit-events?model_name={{ $model }}">Events</a></div>
        <div class="tab-button"><a href="/customizer/edit-actions?model_name={{ $model }}">Actions</a></div>
        <div class="tab-button"><a href="/customizer/edit-pages?model_name={{ $model }}">Pages</a></div>
    </div>

    <form action="{{ url('/customizer/edit-events') }}" method="POST">
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

        <!-- Events Tab -->
        <div id="events" class="tab-content active">
            <h2>Events</h2>
            <div id="active-list">
                <!-- Existing events will be displayed here -->
                @if(!empty($existingEvents))
                    @foreach($existingEvents as $event)
                        <div class="form-group inline-group">
                            <input type="text" name="event_name[]" value="{{ $event->name }}" placeholder="Event Name" required>
                            <select name="event_type[]" onchange="handleEventTypeChange(this)">
                                <option value="update" {{ $event->type == 'update' ? 'selected' : '' }}>Update</option>
                                <option value="delete" {{ $event->type == 'delete' ? 'selected' : '' }}>Delete</option>
                            </select>
                            <select name="scope[]" onchange="handleScopeChange(this)">
                                <option value="entire_model" {{ $event->scope == 'entire_model' ? 'selected' : '' }}>Entire Model</option>
                                <option value="specific_relation" {{ $event->scope == 'specific_relation' ? 'selected' : '' }}>Specific Relation</option>
                            </select>
                            <select name="field[]" style="{{ $event->type == 'update' && $event->scope == 'entire_model' ? 'display:block;' : 'display:none;' }}" class="field-select">
                                @foreach($modelFields as $field)
                                    <option value="{{ $field['name'] }}" {{ $event->field == $field['name'] ? 'selected' : '' }}>{{ $field['name'] }}</option>
                                @endforeach
                            </select>
                            <select name="relation_field[]" <?php if($event->scope=='entire_model') { ?>style="display: none;"<?php }else{ ?>style="display: block;"<?php } ?>  class="relation-field-select" onchange="loadRelationSpecificFields(this)">
                                <option value="">-- Select Relation --</option>
                                @foreach($relationships as $relation)
                                    <option value="{{ $relation['name'] }}" {{ $event->relation_field == $relation['name'] ? 'selected' : '' }}>{{ $relation['name'] }}</option>
                                @endforeach
                            </select>
                            <select name="relation_specific_field[]" <?php if($event->relation_specific_field=='') { ?>style="display: none;"<?php }else{ ?>style="display: block;"<?php } ?> class="relation-specific-field-select">
                               <option value="">Select a Specific Field</option>
                               <?php if($event->relation_specific_field!='') { ?>
                               @foreach($relationSpecificFields[$event->relation_field] as $relation)
                                <option value="{{ $relation }}" <?php if($event->relation_specific_field==$relation){ echo 'selected'; } ?>  >{{ $relation }}</option>
                               @endforeach
                           <?php } ?>
                            </select>
                            <button type="button" onclick="removeEvent(this)">Remove</button>
                        </div>
                    @endforeach
                @else
                    <p>No existing events found.</p>
                @endif
            </div>

            <button type="button" class="add-button" onclick="addEvent()">Add Event</button>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Save Model</button>
    </form>
</div>

<script>
    // Object to store the relation-specific fields loaded from the backend
    const relationSpecificFields = @json($relationSpecificFields);

    // Function to dynamically populate relation-specific fields
    function loadRelationSpecificFields(relationSelect) {
        // Find the closest form group to ensure we are working with the correct row
        const formGroup = relationSelect.closest('.form-group');

        // Find the corresponding event_type[] in the same form group
        const eventTypeSelect = formGroup.querySelector('select[name="event_type[]"]');

        // If the event type is "delete", do not load relation-specific fields
        if (eventTypeSelect.value === 'delete') {
            relationSelect.nextElementSibling.style.display = 'none';
            return;
        }

        const relationName = relationSelect.value;
        const relationSpecificFieldSelect = relationSelect.nextElementSibling;

        // Clear any existing options in the specific field dropdown
        relationSpecificFieldSelect.innerHTML = '';

        if (relationName && relationSpecificFields[relationName]) {
            relationSpecificFields[relationName].forEach(field => {
                const option = document.createElement('option');
                option.value = field;
                option.textContent = field;
                relationSpecificFieldSelect.appendChild(option);
            });

            relationSpecificFieldSelect.style.display = 'block'; // Show the field dropdown
        } else {
            relationSpecificFieldSelect.style.display = 'none'; // Hide if no relation is selected
        }
    }



    function addEvent() {
        const container = document.getElementById('active-list');

        const newEvent = document.createElement('div');
        newEvent.classList.add('form-group', 'inline-group');
        newEvent.innerHTML = `
            <input type="text" name="event_name[]" placeholder="Event Name" required>
            <select name="event_type[]" onchange="handleEventTypeChange(this)">
                <option value="update">Update</option>
                <option value="delete">Delete</option>
            </select>
            <select name="scope[]" onchange="handleScopeChange(this)">
                <option value="entire_model">Entire Model</option>
                <option value="specific_relation">Specific Relation</option>
            </select>
            <select name="field[]" style="display: none;" class="field-select">
                @foreach($modelFields as $field)
                    <option value="{{ $field['name'] }}">{{ $field['name'] }}</option>
                @endforeach
            </select>
            <select name="relation_field[]" class="relation-field-select" onchange="loadRelationSpecificFields(this)">
                <option value="">-- Select Relation --</option>
                @foreach($relationships as $relation)
                    <option value="{{ $relation['name'] }}">{{ $relation['name'] }}</option>
                @endforeach
            </select>
            <select name="relation_specific_field[]" style="display: none;" class="relation-specific-field-select">
                <option value="">Select a Specific Field</option>
            </select>
            <button type="button" onclick="removeEvent(this)">Remove</button>
        `;

        container.appendChild(newEvent);
    }

    function handleEventTypeChange(select) {
        const scopeSelect = select.nextElementSibling;
        const fieldSelect = scopeSelect.nextElementSibling;
        const relationFieldSelect = fieldSelect.nextElementSibling;
        const relationSpecificFieldSelect = relationFieldSelect.nextElementSibling;

        if (select.value === 'update') {
            if (scopeSelect.value === 'entire_model') {
                fieldSelect.style.display = 'block';
                relationFieldSelect.style.display = 'none';
                relationSpecificFieldSelect.style.display = 'none';
            } else if (scopeSelect.value === 'specific_relation') {
                fieldSelect.style.display = 'none';
                relationFieldSelect.style.display = 'block';
                loadRelationSpecificFields(relationFieldSelect); // Load fields dynamically when a relation is selected
            }
        } else {
            fieldSelect.style.display = 'none';
            relationFieldSelect.style.display = 'none';
            relationSpecificFieldSelect.style.display = 'none';
        }
    }

    function handleScopeChange(select) {
        const fieldSelect = select.nextElementSibling;
        const relationFieldSelect = fieldSelect.nextElementSibling;
        const relationSpecificFieldSelect = relationFieldSelect.nextElementSibling;

        if (select.value === 'specific_relation') {
            relationFieldSelect.style.display = 'block';
            relationSpecificFieldSelect.style.display = 'none'; // Relation-specific fields will be populated dynamically
            fieldSelect.style.display = 'none';
        } else {
            fieldSelect.style.display = 'block';
            relationFieldSelect.style.display = 'none';
            relationSpecificFieldSelect.style.display = 'none';
        }
    }

    function removeEvent(button) {
        const eventRow = button.parentElement;
        eventRow.remove();
    }
</script>
@endsection
