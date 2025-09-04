@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
<div class="container">
    <h2>Add Position</h2>
    <form action="{{ route('positions.store') }}" method="POST">@csrf
        <select name="department_id" class="form-control mb-2" required>
            <option value="">Select Department</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        <input type="text" name="name" class="form-control mb-2" placeholder="Position Name" required>
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection