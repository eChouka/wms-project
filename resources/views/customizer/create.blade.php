<!-- resources/views/customizer/create.blade.php -->
@extends('layouts.app')

@section('title', 'Add New Model')

@section('content')
<style type="text/css">
    .form-container {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1),
                    0 1px 3px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 20px;
        background-color: #fff;
        max-width: 900px;
        margin: 20px auto;
    }
    .wrapper{
        display: block;
    }

    .button-no-shadow {
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        border: none; /* No border */
        padding: 12px 24px; /* Some padding */
        font-size: 16px; /* Larger font size */
        border-radius: 8px; /* Rounded corners */
        cursor: pointer; /* Pointer/hand icon on hover */
        transition: background-color 0.3s ease; /* Smooth transition on hover */
    }

    .button-no-shadow:hover {
        background-color: #45a049; /* Darker green on hover */
    }

    .button-no-shadow:active {
        transform: translateY(2px); /* Slight push down effect */
    }

    .button-shadow {
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        border: none; /* No border */
        padding: 12px 24px; /* Some padding */
        font-size: 16px; /* Larger font size */
        border-radius: 8px; /* Rounded corners */
        cursor: pointer; /* Pointer/hand icon on hover */
        transition: all 0.3s ease; /* Smooth transition on hover */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2),
                    0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }

    .button-shadow:hover {
        background-color: #45a049; /* Darker green on hover */
        box-shadow: 0 8px 10px rgba(0, 0, 0, 0.2),
                    0 3px 6px rgba(0, 0, 0, 0.1); /* Enhance shadow on hover */
    }

    .button-shadow:active {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2),
                    0 1px 2px rgba(0, 0, 0, 0.1); /* Smaller shadow on click */
        transform: translateY(2px); /* Slight push down effect */
    }

    .field-summary {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
        background-color: #f9f9f9;
    }

    .field-summary div {
        flex: 1;
        text-align: left;
        font-size: 14px;
    }

    .field-summary button {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-left: 10px;
    }

    .field-summary button:hover {
        background-color: #2980b9;
    }

    .add-field {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
        margin-bottom: 20px;
    }

    .add-field:hover {
        background-color: #2980b9;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 8px;
        width: 80%;
        max-width: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<div class="container">
    <div class="row justify-content-center">

    <form action="{{ route('customizer.store') }}" id="cmf" class="form form-container" method="POST">
        @csrf
        <h1 style="text-align: left;">Create New Model</h1>

        <div class="form-group">
            <label for="model_name" style="font-weight: bold;">Model Name:</label>
            <input type="text" name="model_name" class="form-control" id="model_name" required>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <h2>Fields</h2>
            <div id="field-summaries">
                <!-- Field summary items will go here -->
            </div>
            <button type="button" class="add-field button-shadow" onclick="addField()">Add Field</button>
        </div>

        <hr>

        <button type="submit" class="button-no-shadow" style="width: 100%; margin-top: 20px;">Save Model</button>
    </form>

    <!-- Modal for Field Editing -->
    <div id="fieldModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Edit Field</h3>
            <input type="hidden" id="currentFieldId">

            <label for="modal_field_name">Field Name:</label>
            <input type="text" id="modal_field_name" class="form-control" required>

            <label for="modal_field_type" style="margin-top: 10px;">Type:</label>
            <select id="modal_field_type" class="form-control">
                <option value="string">String</option>
                <option value="int">Integer</option>
                <option value="boolean">Boolean</option>
            </select>

            <label for="modal_field_required" style="margin-top: 10px;">Required:</label>
            <select id="modal_field_required" class="form-control">
                <option value="required">Required</option>
                <option value="optional">Not Required</option>
            </select>

            <label for="modal_field_nullable" style="margin-top: 10px;">Nullable:</label>
            <select id="modal_field_nullable" class="form-control">
                <option value="nullable">Nullable</option>
                <option value="not_nullable">Not Nullable</option>
            </select>

            <label for="modal_field_default" style="margin-top: 10px;">Default Value:</label>
            <input type="text" id="modal_field_default" class="form-control" placeholder="Optional default value">

            <button type="button" class="button-shadow" style="margin-top: 20px;" onclick="saveField()">Save Field</button>
        </div>
    </div>

<script>
    let fieldCounter = 0;

    function addField() {
        const fieldId = `field_${fieldCounter++}`;
        const fieldSummaryHtml = `
            <div id="${fieldId}_summary" class="field-summary">
                <div id="${fieldId}_name">Field ${fieldCounter}</div>
                <div id="${fieldId}_type">String</div>
                <div id="${fieldId}_required">Required</div>
                <div id="${fieldId}_nullable">Not Nullable</div>
                <div id="${fieldId}_default"></div>
                <button type="button" onclick="editField('${fieldId}')">Edit</button>
                <button type="button" class="remove-field" onclick="removeElement('${fieldId}_summary')">Remove</button>
            </div>`;
        document.getElementById('field-summaries').insertAdjacentHTML('beforeend', fieldSummaryHtml);
    }

    function editField(fieldId) {
        document.getElementById('currentFieldId').value = fieldId;
        const fieldName = document.getElementById(`${fieldId}_name`).innerText;
        const fieldType = document.getElementById(`${fieldId}_type`).innerText;
        const fieldRequired = document.getElementById(`${fieldId}_required`).innerText;
        const fieldNullable = document.getElementById(`${fieldId}_nullable`).innerText;
        const fieldDefault = document.getElementById(`${fieldId}_default`).innerText;

        document.getElementById('modal_field_name').value = fieldName;
        document.getElementById('modal_field_type').value = fieldType.toLowerCase() || "string";
        document.getElementById('modal_field_required').value = fieldRequired.toLowerCase();
        document.getElementById('modal_field_nullable').value = fieldNullable.toLowerCase().replace(' ', '_');
        document.getElementById('modal_field_default').value = fieldDefault;

        document.getElementById('fieldModal').style.display = "block";
    }

    function saveField() {
        const fieldId = document.getElementById('currentFieldId').value;
        const fieldName = document.getElementById('modal_field_name').value;
        const fieldType = document.getElementById('modal_field_type').value;
        const fieldRequired = document.getElementById('modal_field_required').value === 'required' ? 'Required' : 'Optional';
        const fieldNullable = document.getElementById('modal_field_nullable').value === 'nullable' ? 'Nullable' : 'Not Nullable';
        const fieldDefault = document.getElementById('modal_field_default').value;

        document.getElementById(`${fieldId}_name`).innerText = fieldName;
        document.getElementById(`${fieldId}_type`).innerText = fieldType;
        document.getElementById(`${fieldId}_required`).innerText = fieldRequired;
        document.getElementById(`${fieldId}_nullable`).innerText = fieldNullable;
        document.getElementById(`${fieldId}_default`).innerText = fieldDefault;

        closeModal();
    }

    function closeModal() {
        document.getElementById('fieldModal').style.display = "none";
    }

    function removeElement(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.remove();
        }
    }

    function prepareFormSubmission(event) {
        event.preventDefault();
        console.log("prepareFormSubmission triggered");
        const fields = document.querySelectorAll('.field-summary');
        const formFields = [];

        fields.forEach(field => {
            const fieldId = field.id.split('_summary')[0];
            const fieldName = document.getElementById(`${fieldId}_name`).innerText;
            const fieldType = document.getElementById(`${fieldId}_type`).innerText.toLowerCase();
            const fieldRequired = document.getElementById(`${fieldId}_required`).innerText.toLowerCase();
            const fieldNullable = document.getElementById(`${fieldId}_nullable`).innerText.toLowerCase().replace(' ', '_');
            const fieldDefault = document.getElementById(`${fieldId}_default`).innerText || '';

            formFields.push(`${fieldName}:${fieldType}:${fieldRequired}:${fieldNullable}:${fieldDefault}`);
        });

        if (formFields.length === 0) {
            alert('Please add at least one field.');
            return false;
        }

        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'fields';
        hiddenField.value = JSON.stringify(formFields);
        document.getElementById('cmf').appendChild(hiddenField);
        document.getElementById('cmf').submit();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('cmf');
        form.addEventListener('submit', prepareFormSubmission);
    });
</script>


</div>
</div>
@endsection
