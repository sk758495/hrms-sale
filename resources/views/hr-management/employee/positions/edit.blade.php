<!-- edit.blade.php -->
@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
<div class="container">
    <h2>Edit Position</h2>
    <form action="{{ route('positions.update', $position) }}" method="POST">
        @csrf @method('PUT')
        <select name="department_id" class="form-control mb-2" required>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" @selected($dept->id == $position->department_id)>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
        <input type="text" name="name" value="{{ $position->name }}" class="form-control mb-2" required>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection