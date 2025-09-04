@extends('layouts.hr-app')

@section('title', 'Document Verification')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="bi bi-file-check"></i> Document Verification</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Submitted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->user->name }}</td>
                            <td>{{ $employee->user->email }}</td>
                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                            <td>{{ $employee->position->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $employee->overall_status == 'pending' ? 'warning' : 'info' }}">
                                    {{ ucfirst($employee->overall_status) }}
                                </span>
                            </td>
                            <td>{{ $employee->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('hr.document-verification.show', $employee) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No pending documents for verification</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection