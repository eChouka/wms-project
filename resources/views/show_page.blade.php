@extends('layouts.app')

@section('content')
<style>
    /* Similar styling as in your original file */
    .container {
        margin-top: 30px;
        max-width: 100%;
    }
    .wrapper{
        display: block;
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

<div class="container">
    <h1>{{ ucfirst($relationName) }} for {{ ucfirst($model) }} (ID: {{ $modelInstance->id }})</h1>


    <!-- Related Data Table -->
    @if($relatedData && $relatedData->isNotEmpty())
        <div class="related-table">
            <div class="card">
                <div class="card-header">
                    {{ ucfirst($relationName) }} Details
                </div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                @foreach($relatedData->first()->getAttributes() as $key => $value)
                                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatedData as $item)
                                <tr>
                                    @foreach($item->getAttributes() as $key => $value)
                                        @php
                                            $relatedModelInstance = $item;
                                            $relatedModel = null;
                                            $relatedMethod = $relatedModelInstance->getRelationMethodName($key);

                                            if ($relatedMethod && method_exists($relatedModelInstance, $relatedMethod)) {
                                                $relatedModel = $relatedModelInstance->{$relatedMethod};
                                            }
                                        @endphp

                                        @if($relatedModel)
                                            <td>{{ $relatedModel->name ?? $relatedModel->title ?? $relatedModel->ref }}</td>
                                        @else
                                            <td>{{ $value }}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="actions-container">
        <a href="{{ url('/model/'.$model.'/'.$modelInstance->id) }}" class="btn btn-secondary">Back to {{ ucfirst($model) }} Details</a>
        <a href="{{ url('/model/'.$model.'/'.$modelInstance->id.'/edit') }}" class="btn btn-primary">Edit {{ ucfirst($model) }}</a>
    </div>
</div>

@endsection
