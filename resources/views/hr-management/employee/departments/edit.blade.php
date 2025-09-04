<!-- edit.blade.php -->
@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
<div class="container">
    <h2>Edit Department</h2>
    <form action="{{ route('departments.update', $department->id) }}" method="POST">
        @csrf @method('PUT')
        <input type="text" name="name" value="{{ $department->name }}" class="form-control mb-2" required>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
