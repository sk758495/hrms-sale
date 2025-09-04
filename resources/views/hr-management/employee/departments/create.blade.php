<!-- create.blade.php -->
@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
<div class="container">
    <h2>Add Department</h2>
    <form action="{{ route('departments.store') }}" method="POST">@csrf
        <input type="text" name="name" class="form-control mb-2" placeholder="Department Name" required>
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection