@extends('layouts.app')

@section('content')
<style>
    /* Container Styles */
    .container {
        margin-top: 30px;
        max-width: 1200px;
    }

    .row {
        margin-bottom: 20px;
    }

    /* Heading Style */
    h1 {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
        text-align: center;
    }

    /* Filter Section Styles */
    .filter-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .filter-container .form-control {
        width: 250px;
        margin-right: 10px;
    }
    .filter-container label {
        font-weight: 600;
        color: #2c3e50;
    }

    /* Button Styles */
    .btn-primary, .btn-secondary, .btn-danger {
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-primary {
        background-color: #3498db;
        color: white;
        border: none;
    }
    .btn-primary:hover {
        background-color: #2980b9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    .btn-secondary {
        background-color: #7f8c8d;
        color: white;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #95a5a6;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    .btn-danger {
        background-color: #e74c3c;
        color: white;
        border: none;
    }
    .btn-danger:hover {
        background-color: #c0392b;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        font-size: 16px;
        margin-bottom: 20px;
    }
    th {
        background-color: #2c3e50;
        color: white;
        padding: 12px 15px;
        text-align: left;
        font-weight: 700;
        text-transform: capitalize;
    }
    td {
        background-color: white;
        padding: 12px 15px;
        color: #2c3e50;
        font-weight: 500;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    tr:hover td {
        background-color: #f5f5f5;
    }

    /* Actions Column */
    td.actions {
        white-space: nowrap;
        text-align: center;
    }

    /* Pagination Styles */

    /* Sortable Table Header */
    th.sortable {
        cursor: pointer;
        user-select: none;
    }
    th.sortable .sort-arrow {
        margin-left: 5px;
        font-size: 12px;
    }

    .rounded-l-md{
        width: 45px;
        position: relative;
        float: left;
        margin-top: -10px;
        margin-right: 10px;
    }
    .rounded-r-md{
        width: 45px;
        position: relative;
        float: right;
        margin-left: 0px;
        margin-top: -12px;

    }
    .pagination-txt nav div div p {
        margin-top: 20px;
    }
    .rounded-md{
        margin-right: 10px;
    }

    .filter-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .filter-container input[type="text"] {
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 81.5%;
        margin-left: 1%;
    }
    .filter-container .filter-buttons {
        display: flex;
        align-items: center;
    }
    .filter-container .filter-buttons button {
        margin-right: 10px;
        margin-top: 10px;
    }
    .filter-container .filter-buttons a {
        margin-top: 10px;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <h1>{{ ucfirst($model) }} List</h1>

        <!-- Filter Section -->
        <div class="filter-container">
            <form action="{{ url('/model/'.$model) }}" method="GET" style="width: 100%;">
                <div style="display: flex; align-items: center; padding-left:10px;">
                    <!-- Filter fields loop -->
                    @foreach($searchFields as $field)
                        <div style="margin-bottom: 10px; margin-right: 10px;">
                            <label for="{{ $field }}">{{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</label>
                            @if(array_key_exists($field, $relationsData))
                                <select name="{{ $field }}" id="{{ $field }}" class="form-control">
                                    <option value="">Select {{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</option>
                                    @foreach($relationsData[$field] as $related)
                                        <option value="{{ $related->id }}" {{ request($field) == $related->id ? 'selected' : '' }}>
                                            {{ $related->name ?? $related->title ?? $related->ref ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control" value="{{ request($field) }}" placeholder="Filter by {{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}">
                            @endif
                        </div>
                    @endforeach
                    <div class="filter-buttons">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ url('/model/'.$model) }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Check if data exists -->
        @if($data->isEmpty())
            <a href="{{ url('/model/'.$model . '/create') }}" class="btn btn-primary" style="margin-bottom: 20px; margin-left:78%; width:20%; float:right;">Create New {{ ucfirst($model) }}</a>
            <p>No {{ $model }} found.</p>
        @else
        <div class="row">
            <a href="{{ url('/model/'.$model . '/bulk-import') }}" class="btn btn-primary" style="margin-bottom: 20px; margin-left:55%; width:20%; float:right;">Bulk Import {{ ucfirst($model) }}</a>
             <a href="{{ url('/model/'.$model . '/create') }}" class="btn btn-primary" style="margin-bottom: 20px; margin-left:4%; width:20%; float:right;">Create New {{ ucfirst($model) }}</a>
            <div class="col-md-12" style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            @foreach($fields as $field)
                                <th class="sortable" onclick="sortTable('{{ $field }}')">
                                    {{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}
                                    @if($sortBy == $field)
                                        @if($sortDirection == 'asc')
                                            <span class="sort-arrow">&uarr;</span>
                                        @else
                                            <span class="sort-arrow">&darr;</span>
                                        @endif
                                    @endif
                                </th>
                            @endforeach
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            <tr>
                                @foreach($fields as $field)
                                    @if(array_key_exists($field, $relationsData))
                                        <td>
                                            @php
                                                $relatedItem = $relationsData[$field]->firstWhere('id', $item->$field);
                                            @endphp
                                            {{ $relatedItem->name ?? $relatedItem->title ?? $relatedItem->ref ?? $relatedItem->id ?? 'N/A' }}
                                        </td>
                                    @else
                                        <td>{{ $item->$field }}</td>
                                    @endif
                                @endforeach

                                <!-- Add Page buttons -->
                                <td class="actions">
                                        @foreach($pages as $page)
                                            <a href="{{ url('/model/'.$model . '/' . $item->id . '/' . $page['url']) }}" class="btn btn-secondary btn-sm">
                                                {{ ucfirst($page['name']) }}
                                            </a>
                                        @endforeach

                                    <!-- Existing View, Edit, and Delete buttons -->
                                    <a href="{{ url('/model/'.$model . '/' . $item->id) }}" class="btn btn-primary btn-sm">View</a>
                                    <a href="{{ url('/model/'.$model . '/' . $item->id . '/edit') }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ url('/model/'.$model . '/' . $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Links -->
            <div class="pagination-txt">
                {{ $data->links('pagination::tailwind') }}
            </div>
        </div>
        @endif

    </div>
</div>

<script>
    function sortTable(field) {
        let currentUrl = new URL(window.location.href);
        let currentSortBy = currentUrl.searchParams.get('sort_by');
        let currentSortDirection = currentUrl.searchParams.get('sort_direction');

        if (currentSortBy === field) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortDirection = 'asc';
        }

        currentUrl.searchParams.set('sort_by', field);
        currentUrl.searchParams.set('sort_direction', currentSortDirection);
        currentUrl.searchParams.set('page', 1);  // Reset to first page

        window.location.href = currentUrl.toString();
    }
</script>

@endsection

