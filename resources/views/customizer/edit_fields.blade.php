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
        <div class="tab-button active" data-tab="fields"><a href="/customizer/edit-fields?model_name={{ $model }}">Fields</a></div>
        <div class="tab-button" data-tab="relationships"><a href="/customizer/edit-relations?model_name={{ $model }}">Relationships</a></div>
        <div class="tab-button" data-tab="events"><a href="/customizer/edit-events?model_name={{ $model }}">Events</a></div>
        <div class="tab-button" data-tab="actions"><a href="/customizer/edit-actions?model_name={{ $model }}">Actions</a></div>
        <div class="tab-button" data-tab="pages"><a href="/customizer/edit-pages?model_name={{ $model }}">Pages</a></div>
    </div>

    <form action="{{ url('/customizer/edit-fields') }}" method="POST">
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

        <!-- Fields Tab -->
        <div id="fields" class="tab-content active">
            <h2>Fields</h2>
            <div id="field-list">
                @foreach($fields as $field)
                <div class="form-group">
                    <label for="field_name_{{ $loop->index }}">Field Name:</label>
                    <input type="text" name="fields[{{ $loop->index }}][name]" value="{{ $field['name'] }}" required>

                    <label for="field_type_{{ $loop->index }}">Type:</label>
                    <select name="fields[{{ $loop->index }}][type]" required>
                        <option value="string" {{ $field['type'] == 'string' ? 'selected' : '' }}>String</option>
                        <option value="integer" {{ $field['type'] == 'integer' ? 'selected' : '' }}>Integer</option>
                        <option value="boolean" {{ $field['type'] == 'boolean' ? 'selected' : '' }}>Boolean</option>
                    </select>

                    <label for="field_required_{{ $loop->index }}">Required:</label>
                    <select name="fields[{{ $loop->index }}][required]" required>
                        <option value="required" {{ $field['required'] ? 'selected' : '' }}>Required</option>
                        <option value="optional" {{ !$field['required'] ? 'selected' : '' }}>Optional</option>
                    </select>

                    <label for="field_default_{{ $loop->index }}">Default Value:</label>
                    <input type="text" name="fields[{{ $loop->index }}][default]" value="{{ $field['default'] }}">

                    <!-- New Checkbox for Search Filter -->
                    <label for="search_field_{{ $loop->index }}">Search Filter:</label>
                    <input type="checkbox" style="width:50px;" name="fields[{{ $loop->index }}][search_field]" {{ $field['search_field'] ?? false ? 'checked' : '' }}>


                    <button type="button" onclick="removeField(this)">Delete Field</button>
                </div>
                @endforeach
            </div>
            <button type="button" class="add-button" onclick="addField()">Add Field</button>
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

    function addField() {
        const fieldList = document.getElementById('field-list');
        const index = fieldList.children.length;
        const fieldHtml = `
            <div class="form-group">
                <label for="field_name_${index}">Field Name:</label>
                <input type="text" name="fields[${index}][name]" required>

                <label for="field_type_${index}">Type:</label>
                <select name="fields[${index}][type]" required>
                    <option value="string">String</option>
                    <option value="integer">Integer</option>
                    <option value="boolean">Boolean</option>
                </select>

                <label for="field_required_${index}">Required:</label>
                <select name="fields[${index}][required]" required>
                    <option value="required">Required</option>
                    <option value="optional">Optional</option>
                </select>

                <label for="field_nullable_${index}">Nullable:</label>
                <select name="fields[${index}][nullable]" required>
                    <option value="nullable">Nullable</option>
                    <option value="not_nullable">Not Nullable</option>
                </select>

                <label for="field_default_${index}">Default Value:</label>
                <input type="text" name="fields[${index}][default]">

                <label for="search_field_${index}">Search Filter:</label>
                <input type="checkbox" name="fields[${index}][search_field]">

                <button type="button" onclick="removeField(this)">Delete Field</button>
            </div>`;
        fieldList.insertAdjacentHTML('beforeend', fieldHtml);
    }

    function removeField(button) {
        button.parentElement.remove();
    }
</script>
@endsection
