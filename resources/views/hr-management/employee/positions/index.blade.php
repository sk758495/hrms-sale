@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
    <div class="container">
        <h2>Positions</h2>
        <a href="{{ route('positions.create') }}" class="btn btn-primary mb-3">Add Position</a>
        @foreach ($positions as $position)
            <div class="card p-3 mb-2" style="display: flex;">
                <div>
                    <strong>{{ $position->name }}</strong> ({{ $position->department->name }})
                </div>
                <div>
                    <a href="{{ route('positions.edit', $position) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('positions.destroy', $position) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
