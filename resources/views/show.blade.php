@extends('layouts.app')

@section('content')
<style>
    /* Container */
    .container {
        margin-top: 30px;
        max-width: 1000px;
    }

    h1 {
        font-size: 28px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 30px;
        text-align: center;
    }

    /* Card Styles */
    .card {
        background-color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: none;
    }

    .card-header {
        font-size: 18px;
        font-weight: 600;
        color: #3498db;
        margin-bottom: 15px;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }

    .card-body {
        display: flex;
        flex-wrap: wrap;
    }

    .card-body .field-group {
        width: 50%;
        padding: 10px;
    }

    .card-body .field-group th,
    .card-body .field-group td {
        padding: 5px 0;
    }

    .card-body .field-group th {
        font-weight: 600;
        color: #2c3e50;
    }

    .card-body .field-group td {
        color: #7f8c8d;
    }

    /* Related Table Styles */
    .related-table {
        margin-top: 20px;
        padding: 0px;
    }

    .related-table table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .related-table th, .related-table td {
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
    }

    .related-table th {
        background-color: #2980b9;
        color: white;
        font-weight: 600;
        text-transform: capitalize;
    }

    .related-table td {
        background-color: white;
        color: #34495e;
        font-weight: 500;
        border-bottom: 1px solid #ddd;
    }

    .related-table tr:last-child td {
        border-bottom: none;
    }

    /* Action Buttons */
    .actions-container {
        text-align: center;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        margin: 0 10px;
    }

    .btn-primary {
        background-color: #3498db;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-danger {
        background-color: #e74c3c;
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c0392b;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.inline-edit').forEach(element => {
        element.addEventListener('change', function() {
            const field = this.getAttribute('data-field');
            const id = this.getAttribute('data-id');
            const value = this.value;

            // Send AJAX request to update the field
            fetch(`/model/{{ $model }}/quick-update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    field: field,
                    value: value,
                    model:'{{ $model}}',
                    id:id,

                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    alert('Field updated successfully');
                }
            }).catch(error => {
                console.error('Error updating field:', error);
            });
        });
    });
});
</script>
<div class="row">
    <div class="row justify-content-center">
        <h1>{{ ucfirst($model) }} Details</h1>

        <!-- Main Details Card -->
        <div class="card">
            <div class="card-header">
                Main Information
            </div>
            <div class="card-body">
                @foreach($modelInstance->getAttributes() as $field => $value)
                    <div class="field-group">
                        <th>{{ $readableColumns[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</th>
                        <td>
                            @if(array_key_exists($field, $relationsData))
                                @php
                                    $relatedItem = $relationsData[$field]->firstWhere('id', $value);
                                @endphp
                                @if(isset($events) && $events->where('field', $field)->where('type', 'update')->isNotEmpty())
                                    <select class="inline-edit" data-field="{{ $field }}" data-id="{{ $modelInstance->id }}">
                                        @foreach($relationsData[$field] as $related)
                                            <option value="{{ $related->id }}" {{ $related->id == $value ? 'selected' : '' }}>
                                                {{ $related->name ?? $related->title ?? $related->ref }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    {{ $relatedItem->name ?? $relatedItem->title ?? $relatedItem->ref ?? 'N/A' }}
                                @endif
                            @else
                                @if(isset($events) && $events->where('field', $field)->where('type', 'update')->isNotEmpty())
                                    <input type="text" class="inline-edit" data-field="{{ $field }}" data-id="{{ $modelInstance->id }}" value="{{ $value }}">
                                @else
                                    {{ $value }}
                                @endif
                            @endif
                        </td>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Related Items Section -->
        @foreach($relationsData as $foreignKey => $relatedItems)
            @php
                $relationMethod = $modelInstance->getRelationMethodName($foreignKey);
                if ($relationMethod && method_exists($modelInstance, $relationMethod)) {
                    $relationInstance = $modelInstance->{$relationMethod}();
                    $isHasMany = $relationInstance instanceof \Illuminate\Database\Eloquent\Relations\HasMany;
                } else {
                    continue;
                }
                $filteredItems = $modelInstance->{$relationMethod};
            @endphp

            @if($isHasMany && $filteredItems->isNotEmpty())
                <div class="related-table">
                    <div class="card">
                        <div class="card-header">
                            {{ ucfirst(str_replace('_', ' ', $relationMethod)) }}
                        </div>
                        <div class="card-body">
                            <table>
                                <thead>
                                    <tr>
                                        @foreach($filteredItems->first()->getAttributes() as $key => $value)
                                            <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                        @endforeach
                                        @if(isset($events) && $events->where('relation', $foreignKey)->where('type', 'delete')->isNotEmpty())
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filteredItems as $item)
                                        <tr>
                                            @foreach($item->getAttributes() as $key => $value)
                                                @php
                                                    $currentModelInstance = $item;
                                                    $relatedModel = null;
                                                    $relatedMethod = $currentModelInstance->getRelationMethodName($key);

                                                    if ($relatedMethod && method_exists($currentModelInstance, $relatedMethod)) {
                                                        $relatedModel = $currentModelInstance->{$relatedMethod};
                                                    }
                                                @endphp

                                                @if($relatedModel)
                                                    <td>{{ $relatedModel->name ?? $relatedModel->title ?? $relatedModel->ref }}</td>
                                                @else
                                                    <td>{{ $value }}</td>
                                                @endif
                                            @endforeach
                                             @if(isset($events) && $events->where('relation', $foreignKey)->where('type', 'delete')->isNotEmpty())
                                                <td>
                                                    <form action="{{ url('/model/'.$model . '/' . $item->id.'/delete-relation') }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn-delete-row">Delete</button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Action Buttons -->
        <div class="actions-container">
            <a href="{{ url('/model/'.$model . '/' . $modelInstance->id . '/edit') }}" class="btn btn-primary">Edit</a>
            <form action="{{ url('/model/'.$model . '/' . $modelInstance->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <a href="{{ url('/model/'.$model) }}" class="btn btn-primary">Back to {{ ucfirst($model) }} List</a>
        </div>
    </div>
</div>
@endsection
