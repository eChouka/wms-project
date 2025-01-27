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
        <div class="tab-button"><a href="/customizer/edit-events?model_name={{ $model }}">Events</a></div>
        <div class="tab-button"><a href="/customizer/edit-actions?model_name={{ $model }}">Actions</a></div>
        <div class="tab-button active"><a href="/customizer/edit-pages?model_name={{ $model }}">Pages</a></div>
    </div>

    <form action="{{ url('/customizer/edit-pages') }}" method="POST">
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

        <!-- Pages Tab -->
        <div id="pages" class="tab-content active">
            <h2>Pages</h2>

            <div id="active-list">
                @if(!empty($existingPages))
                    @foreach($existingPages as $page)
                        <div class="form-group inline-group">
                            <input type="text" name="pages[]" value="{{ $page->name }}" placeholder="Page Name" required>
                            <input type="text" name="paths[]" value="{{ $page->url }}" placeholder="Page Path/URL" required>
                            <select name="page_type[]" onchange="handlePageTypeChange(this)">
                                <option value="0" {{ $page->entire_model == 0 ? 'selected' : '' }}>Entire Model</option>
                                <option value="1" {{ $page->entire_model == 1 ? 'selected' : '' }}>Model Relation</option>
                                <option value="2" {{ $page->entire_model == 2 ? 'selected' : '' }}>Code Execution</option>
                            </select>
                            <select name="relation[]" class="relation-select" style="{{ $page->entire_model == 1 ? 'display:block;' : 'display:none;' }}">
                                @foreach($relationships as $relationship)
                                    <option value="{{ $relationship['name'] }}" {{ $page->model_relation == $relationship['name'] ? 'selected' : '' }}>{{ $relationship['name'] }}</option>
                                @endforeach
                            </select>
                            <textarea name="custom_code[]" class="code-textarea" style="{{ $page->entire_model == 2 ? 'display:block; width:40%;' : 'display:none;' }}" placeholder="Enter your code">{{ $page->custom_code }}</textarea>
                            <button type="button" onclick="removePage(this)">Remove</button>
                        </div>
                    @endforeach
                @else
                    <p>No existing pages found.</p>
                @endif
            </div>

            <button type="button" class="add-button" onclick="addPage()">Add Page</button>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Save Model</button>
    </form>
</div>

<script>
    function addPage() {
        const container = document.getElementById('active-list');

        const newPage = document.createElement('div');
        newPage.classList.add('form-group', 'inline-group');
        newPage.innerHTML = `
            <input type="text" name="pages[]" placeholder="Page Name" required>
            <input type="text" name="paths[]" placeholder="Page Path/URL" required>
            <select name="page_type[]" onchange="handlePageTypeChange(this)">
                <option value="0">Entire Model</option>
                <option value="1">Model Relation</option>
                <option value="2">Code Execution</option>
            </select>
            <select name="relation[]" style="display: none;" class="relation-select">
                @foreach($relationships as $relationship)
                    <option value="{{ $relationship['name']??'' }}">{{ $relationship['name']??'' }}</option>
                @endforeach
            </select>
            <textarea name="custom_code[]" style="display: none; width:40%;" class="code-textarea" placeholder="Enter your code"></textarea>
            <button type="button" onclick="removePage(this)">Remove</button>
        `;

        container.appendChild(newPage);
    }

    function handlePageTypeChange(select) {
        const relationSelect = select.nextElementSibling;
        const codeTextarea = relationSelect.nextElementSibling;

        if (select.value === '1') {
            relationSelect.style.display = 'block';
            codeTextarea.style.display = 'none';
        } else if (select.value === '2') {
            relationSelect.style.display = 'none';
            codeTextarea.style.display = 'block';
        } else {
            relationSelect.style.display = 'none';
            codeTextarea.style.display = 'none';
        }
    }

    function removePage(button) {
        const pageRow = button.parentElement;
        pageRow.remove();
    }
</script>
@endsection
