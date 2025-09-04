
@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Departments</h2>
        <a href="{{ route('departments.create') }}" class="btn btn-primary">+ Add Department</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($departments->count())
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Department Name</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $key => $department)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No departments found.</p>
    @endif
</div>

@endsection