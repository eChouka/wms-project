@extends('layouts.app')

@section('content')
<style>
    .container {
        margin-top: 30px;
        max-width: 1200px;
    }
    h1 {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 30px;
        text-align: center;
    }
    .table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: separate;
        border-spacing: 0 8px;
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
    input[type="file"] {
        display: block;
        margin-bottom: 20px;
    }
    .btn-primary {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        margin-top: 20px;
    }
    .btn-primary:hover {
        background-color: #2980b9;
    }
    .wrapper{
        display: block;
    }
</style>

<div class="container">
    <h1>Bulk Import for {{ ucfirst($model) }}</h1>

    <form action="{{ url('/model/'.$model.'/bulk-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <h4>Expected File Structure</h4>
            <table class="table">
                <thead>
                    <tr>
                        @foreach($fields as $field)
                            @if($field!='id' && $field!='created_at' && $field!='updated_at')
                            <th>{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($fields as $field)
                            @if($field!='id' && $field!='created_at' && $field!='updated_at')
                            <td>Test Data {{ $loop->index + 1 }}</td>
                             @endif
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <label for="file-upload">Upload CSV File</label>
            <input type="file" name="file" id="file-upload" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload File</button>
    </form>
</div>
@endsection
