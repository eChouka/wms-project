<!-- resources/views/customizer/index.blade.php -->
@extends('layouts.app')

@section('title', 'Customizer')

@section('content')
<style>
    .customizer-container {
        display: flex;
        justify-content: space-between;
        padding: 40px;
    }
    .wrapper{
        display: block;
    }
    .customizer-box {
        flex: 1;
        margin: 10px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1),
                    0 1px 3px rgba(0, 0, 0, 0.08);
        background-color: #fff;
        text-align: center;
    }

    .customizer-box h2 {
        margin-bottom: 20px;
    }

    .customizer-box a, .customizer-box button {
        display: block;
        margin: 20px auto;
        padding: 12px 24px;
        font-size: 16px;
        color: white;
        background-color: #3498db;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .customizer-box a:hover, .customizer-box button:hover {
        background-color: #2980b9;
    }

    .customizer-box form {
        margin-top: 20px;
    }

    .customizer-box select {
        padding: 10px;
        font-size: 16px;
        width: 100%;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
</style>

<div class="container">
    <h1 style="text-align: center;">Customizer</h1>
    <div class="customizer-container">
        <!-- Add New Model Section -->
        <div class="customizer-box">
            <h2>Add New Model</h2>
            <a href="{{ url('/customizer/create') }}">Create New Model</a>
        </div>

        <!-- Edit Existing Model Section -->
        <div class="customizer-box">
            <h2>Edit Existing Model</h2>
            <form action="{{ url('/customizer/edit-fields') }}" method="GET">
                <select name="model_name" required>
                    <option value="" disabled selected>Select an Existing Model</option>
                    <!-- Dynamically populate this with model names -->
                    @foreach($models as $model)
                        <option value="{{ $model }}">{{ $model }}</option>
                    @endforeach
                </select>
                <button type="submit">Edit Model</button>
            </form>
        </div>
    </div>
</div>
@endsection
