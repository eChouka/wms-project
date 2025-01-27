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

        <!-- Actions Tab -->
<div id="actions" class="tab-content active">
    <h2>Actions</h2>
    <div id="action-list">
        @foreach($actions as $index => $action)
        <div class="form-group action-container">
            <label for="action_type_{{ $index }}">Action Type:</label>
            <select name="actions[{{ $index }}][action_type]" required onchange="toggleActionFields(this, {{ $index }})">
                <option value="post_request" {{ $action->action_type == 'post_request' ? 'selected' : '' }}>POST Request</option>
                <option value="get_request" {{ $action->action_type == 'get_request' ? 'selected' : '' }}>GET Request</option>
                <option value="put_request" {{ $action->action_type == 'put_request' ? 'selected' : '' }}>PUT Request</option>
                <option value="delete_request" {{ $action->action_type == 'delete_request' ? 'selected' : '' }}>DELETE Request</option>
                <option value="send_email" {{ $action->action_type == 'send_email' ? 'selected' : '' }}>Send Email</option>
                <option value="send_notification" {{ $action->action_type == 'send_notification' ? 'selected' : '' }}>Send Notification</option>
                <option value="custom" {{ $action->action_type == 'custom' ? 'selected' : '' }}>Custom Action</option>
            </select>

            <label for="action_name_{{ $index }}">Action Name:</label>
            <input type="text" name="actions[{{ $index }}][name]" value="{{ $action->name }}" required>

            <label for="action_event_{{ $index }}">Event:</label>
            <select name="actions[{{ $index }}][event]" required>
                <option value="on_create" {{ $action->event == 'on_create' ? 'selected' : '' }}>On Create</option>
                <option value="on_update" {{ $action->event == 'on_update' ? 'selected' : '' }}>On Update</option>
                <option value="on_delete" {{ $action->event == 'on_delete' ? 'selected' : '' }}>On Delete</option>
                <option value="on_login" {{ $action->event == 'on_login' ? 'selected' : '' }}>On Login</option>
                <option value="on_logout" {{ $action->event == 'on_logout' ? 'selected' : '' }}>On Logout</option>
            </select>

            <label for="action_where_model_{{ $index }}">Which Model:</label>
            <select name="actions[{{ $index }}][where][model]" required onchange="loadModelFields(this, {{ $index }})">
                <option value="">Select Model</option>
                @foreach($availableModels as $availableModel)
                    <option value="{{ $availableModel }}" {{ $action->where_model == $availableModel ? 'selected' : '' }}>
                        {{ $availableModel }}
                    </option>
                @endforeach
            </select>

            <div id="action-where-fields-{{ $index }}" class="action-where-fields">
                <label for="action_where_field_{{ $index }}">Where Field:</label>
                <select name="actions[{{ $index }}][where][field]" required>
                    @foreach($fields as $field)
                        <option value="{{ $field['name'] }}" {{ $action->where_field == $field['name'] ? 'selected' : '' }}>
                            {{ $field['name'] }}
                        </option>
                    @endforeach
                </select>

                <label for="action_where_value_{{ $index }}">Equals:</label>
                <div class="form-group inline-group">
                    <input type="radio" name="actions[{{ $index }}][where][value_type]" value="field" onchange="toggleWhereValueType(this, {{ $index }})" {{ $action->where_value ? 'checked' : '' }}>
                    <select name="actions[{{ $index }}][where][value]" {{ $action->where_value ? '' : 'disabled' }}>
                        @foreach($fields as $field)
                            <option value="{{ $field['name'] }}" {{ $action->where_value == $field['name'] ? 'selected' : '' }}>
                                {{ $field['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="radio" name="actions[{{ $index }}][where][value_type]" value="custom" onchange="toggleWhereValueType(this, {{ $index }})" {{ $action->where_custom_value ? 'checked' : '' }}>
                    <input type="text" name="actions[{{ $index }}][where][custom_value]" value="{{ $action->where_custom_value }}" {{ $action->where_custom_value ? '' : 'disabled' }} placeholder="Custom Value">
                </div>
            </div>

            <!-- Default fields for HTTP requests -->
            <div id="action-default-fields-{{ $index }}" class="action-default-fields" style="display: {{ strpos($action->action_type, '_request') !== false ? 'block' : 'none' }}">
                <label for="action_url_{{ $index }}">URL:</label>
                <input type="text" name="actions[{{ $index }}][url]" value="{{ $action->url ?? '' }}">

                <div id="action-data-{{ $index }}">
                    @if(isset($action->data['params']))
                        @foreach($action->data['params'] as $paramIndex => $param)
                        <div class="form-group inline-group">
                            <input type="radio" name="actions[{{ $index }}][data][params][{{ $paramIndex }}][type]" value="field" onchange="toggleDataType(this, {{ $index }}, {{ $paramIndex }})" {{ isset($param['name']) ? 'checked' : '' }}>
                            <select name="actions[{{ $index }}][data][params][{{ $paramIndex }}][name]" {{ isset($param['name']) ? '' : 'disabled' }}>
                                @foreach($fields as $field)
                                    <option value="{{ $field['name'] }}" {{ $param['name'] == $field['name'] ? 'selected' : '' }}>
                                        {{ $field['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="radio" name="actions[{{ $index }}][data][params][{{ $paramIndex }}][type]" value="custom" onchange="toggleDataType(this, {{ $index }}, {{ $paramIndex }})" {{ isset($param['custom_name']) ? 'checked' : '' }}>
                            <input type="text" name="actions[{{ $index }}][data][params][{{ $paramIndex }}][custom_name]" value="{{ $param['custom_name'] ?? '' }}" placeholder="Custom Data Name" {{ isset($param['custom_name']) ? '' : 'disabled' }}>
                            <label>Value:</label>
                            <input type="text" name="actions[{{ $index }}][data][params][{{ $paramIndex }}][value]" value="{{ $param['value'] }}" required>

                            <button type="button" onclick="removeActionData(this)">Remove</button>
                        </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="add-button" onclick="addActionData({{ $index }})">Add Data</button>
            </div>

            <!-- Custom action code -->
            <div id="action-custom-fields-{{ $index }}" class="action-custom-fields" style="display: {{ $action->action_type == 'custom' ? 'block' : 'none' }}">
                <label for="action_content_{{ $index }}">Custom Code:</label>
                <textarea name="actions[{{ $index }}][content]" rows="5">{{ $action->custom_code }}</textarea>
            </div>

            <!-- Email action fields -->
            <div id="action-email-fields-{{ $index }}" class="action-email-fields" style="display: {{ $action->action_type == 'send_email' ? 'block' : 'none' }}">
                <label for="email_title_{{ $index }}">Email Title:</label>
                <input type="text" name="actions[{{ $index }}][email_title]" value="{{ $action->email_title }}">

                <label for="email_content_{{ $index }}">Email Content:</label>
                <textarea name="actions[{{ $index }}][email_content]" rows="5">{{ $action->email_content }}</textarea>
            </div>

            <!-- Notification action fields -->
            <div id="action-notification-fields-{{ $index }}" class="action-notification-fields" style="display: {{ $action->action_type == 'send_notification' ? 'block' : 'none' }}">
                <label for="notification_content_{{ $index }}">Notification Content:</label>
                <textarea name="actions[{{ $index }}][notification_content]" rows="3">{{ $action->notification_content }}</textarea>
            </div>

            <button type="button" onclick="removeAction(this)">Delete Action</button>
        </div>
        @endforeach
    </div>
    <button type="button" class="add-button" onclick="addAction()">Add New Action</button>
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

    function addAction() {
        const actionList = document.getElementById('action-list');
        const index = actionList.children.length;
        const actionHtml = `
            <div class="form-group action-container">
                <label for="action_type_${index}">Action Type:</label>
                <select name="actions[${index}][action_type]" required onchange="toggleActionFields(this, ${index})">
                    <option value="post_request">POST Request</option>
                    <option value="get_request">GET Request</option>
                    <option value="put_request">PUT Request</option>
                    <option value="delete_request">DELETE Request</option>
                    <option value="send_email">Send Email</option>
                    <option value="send_notification">Send Notification</option>
                    <option value="custom">Custom Action</option>
                </select>

                <label for="action_name_${index}">Action Name:</label>
                <input type="text" name="actions[${index}][name]" required>

                <label for="action_event_${index}">Event:</label>
                <select name="actions[${index}][event]" required>
                    <option value="on_create">On Create</option>
                    <option value="on_update">On Update</option>
                    <option value="on_delete">On Delete</option>
                    <option value="on_login">On Login</option>
                    <option value="on_logout">On Logout</option>
                </select>

                <label for="action_where_model_${index}">Which Model:</label>
                <select name="actions[${index}][where][model]" required onchange="loadModelFields(this, ${index})">
                    <option value="">Select Model</option>
                    @foreach($availableModels as $availableModel)
                        <option value="{{ $availableModel }}">{{ $availableModel }}</option>
                    @endforeach
                </select>

                <div id="action-where-fields-${index}" class="action-where-fields">
                    <label for="action_where_field_${index}">Where Field:</label>
                    <select name="actions[${index}][where][field]" required>
                        <!-- Fields of the selected model will be populated here via JavaScript -->
                    </select>

                    <label for="action_where_value_${index}">Equals:</label>
                    <div class="form-group inline-group">
                        <input type="radio" name="actions[${index}][where][value_type]" value="field" onchange="toggleWhereValueType(this, ${index})">
                        <select name="actions[${index}][where][value]" disabled>
                            @foreach($fields as $field)
                                <option value="{{ $field['name'] }}">{{ $field['name'] }}</option>
                            @endforeach
                        </select>
                        <input type="radio" name="actions[${index}][where][value_type]" value="custom" onchange="toggleWhereValueType(this, ${index})">
                        <input type="text" name="actions[${index}][where][custom_value]" placeholder="Custom Value" disabled>
                    </div>
                </div>

                <div id="action-default-fields-${index}" class="action-default-fields" style="display: none;">
                    <label for="action_url_${index}">URL:</label>
                    <input type="text" name="actions[${index}][url]">

                    <div id="action-data-${index}">
                    </div>
                    <button type="button" class="add-button" onclick="addActionData(${index})">Add Data</button>
                </div>

                <div id="action-custom-fields-${index}" class="action-custom-fields" style="display: none;">
                    <label for="action_content_${index}">Custom Code:</label>
                    <textarea name="actions[${index}][content]" rows="5"></textarea>
                </div>

                <div id="action-email-fields-${index}" class="action-email-fields" style="display: none;">
                    <label for="email_title_${index}">Email Title:</label>
                    <input type="text" name="actions[${index}][email_title]">

                    <label for="email_content_${index}">Email Content:</label>
                    <textarea name="actions[${index}][email_content]" rows="5"></textarea>
                </div>

                <div id="action-notification-fields-${index}" class="action-notification-fields" style="display: none;">
                    <label for="notification_content_${index}">Notification Content:</label>
                    <textarea name="actions[${index}][notification_content]" rows="3"></textarea>
                </div>

                <button type="button" onclick="removeAction(this)">Delete Action</button>
            </div>`;
        actionList.insertAdjacentHTML('beforeend', actionHtml);
    }

    function removeAction(button) {
        button.parentElement.remove();
    }

    function addActionData(actionIndex) {
        const actionDataList = document.getElementById(`action-data-${actionIndex}`);
        const dataIndex = actionDataList.children.length;
        const dataHtml = `
            <div class="form-group inline-group">
                <input type="radio" name="actions[${actionIndex}][data][params][${dataIndex}][type]" value="field" onchange="toggleDataType(this, ${actionIndex}, ${dataIndex})">
                <select name="actions[${actionIndex}][data][params][${dataIndex}][name]" disabled>
                    @foreach($fields as $field)
                        <option value="{{ $field['name'] }}">{{ $field['name'] }}</option>
                    @endforeach
                </select>
                <input type="radio" name="actions[${actionIndex}][data][params][${dataIndex}][type]" value="custom" onchange="toggleDataType(this, ${actionIndex}, ${dataIndex})">
                <input type="text" name="actions[${actionIndex}][data][params][${dataIndex}][custom_name]" placeholder="Custom Data Name" disabled>

                <label>Value:</label>
                <input type="text" name="actions[${actionIndex}][data][params][${dataIndex}][value]" required>

                <button type="button" onclick="removeActionData(this)">Remove</button>
            </div>`;
        actionDataList.insertAdjacentHTML('beforeend', dataHtml);
    }

    function removeActionData(button) {
        button.parentElement.remove();
    }

    function toggleWhereValueType(radio, index) {
        const whereFields = document.querySelector(`#action-where-fields-${index}`);
        const selectField = whereFields.querySelector('select[name="actions[' + index + '][where][value]"]');
        const inputField = whereFields.querySelector('input[name="actions[' + index + '][where][custom_value]"]');

        if (radio.value === 'field') {
            selectField.disabled = false;
            inputField.disabled = true;
            inputField.value = '';
        } else if (radio.value === 'custom') {
            selectField.disabled = true;
            selectField.value = '';
            inputField.disabled = false;
        }
    }

    function toggleDataType(radio, actionIndex, dataIndex) {
        const dataFields = document.querySelector(`#action-data-${actionIndex}`);
        const selectField = dataFields.querySelector('select[name="actions[' + actionIndex + '][data][params][' + dataIndex + '][name]"]');
        const inputField = dataFields.querySelector('input[name="actions[' + actionIndex + '][data][params][' + dataIndex + '][custom_name]"]');

        if (radio.value === 'field') {
            selectField.disabled = false;
            inputField.disabled = true;
            inputField.value = '';
        } else if (radio.value === 'custom') {
            selectField.disabled = true;
            selectField.value = '';
            inputField.disabled = false;
        }
    }

    function loadModelFields(select, index) {
        const selectedModel = select.value;
        const fieldsSelect = document.querySelector(`#action-where-fields-${index} select[name="actions[${index}][where][field]"]`);

        fieldsSelect.innerHTML = '';
        const fields = ['id', 'name', 'created_at'];
        fields.forEach(field => {
            const option = document.createElement('option');
            option.value = field;
            option.textContent = field;
            fieldsSelect.appendChild(option);
        });
    }

    function toggleActionFields(select, index) {
        const actionType = select.value;
        const defaultFields = document.getElementById(`action-default-fields-${index}`);
        const customFields = document.getElementById(`action-custom-fields-${index}`);
        const emailFields = document.getElementById(`action-email-fields-${index}`);
        const notificationFields = document.getElementById(`action-notification-fields-${index}`);

        // Hide all fields initially
        defaultFields.style.display = 'none';
        customFields.style.display = 'none';
        emailFields.style.display = 'none';
        notificationFields.style.display = 'none';

        // Show the relevant fields based on the selected action type
        if (actionType.includes('_request')) {
            defaultFields.style.display = 'block';
        } else if (actionType === 'custom') {
            customFields.style.display = 'block';
        } else if (actionType === 'send_email') {
            emailFields.style.display = 'block';
        } else if (actionType === 'send_notification') {
            notificationFields.style.display = 'block';
        }
    }

</script>
@endsection
