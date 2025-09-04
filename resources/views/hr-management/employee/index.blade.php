@extends('layouts.hr-app')

@section('title', 'Employee Management')

@push('styles')
<style>
    .employee-card {
        border: none;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        background-color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .employee-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .employee-header {
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
        color: #2563eb;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .employee-section {
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    
    .employee-section strong {
        display: inline-block;
        width: 200px;
        font-weight: 600;
        color: #374151;
        flex-shrink: 0;
    }
    
    .employee-doc {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .employee-doc a {
        display: inline-block;
        margin-right: 0.75rem;
        margin-bottom: 0.5rem;
        color: #2563eb;
        text-decoration: none;
        padding: 0.5rem 1rem;
        background-color: #eff6ff;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .employee-doc a:hover {
        background-color: #2563eb;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-actions {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Employee Records</h2>
    <a href="{{ route('employee.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Add New Employee
    </a>
</div>

@foreach ($employees as $emp)
    <div class="employee-card">
        <div class="employee-header">
            {{ $emp->user->name ?? 'N/A' }} - {{ ucfirst($emp->experience_type) }}
        </div>

        <div class="employee-section"><strong>Employee Id:</strong> {{ $emp->user->employee_id ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Mobile:</strong> {{ $emp->user->mobile ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Email:</strong> {{ $emp->user->email ?? 'N/A' }}</div>
        <div class="employee-section"><strong>CTC:</strong> {{ $emp->ctc ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Department:</strong> {{ $emp->department->name ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Position:</strong> {{ $emp->position->name ?? 'N/A' }}</div>

        <div class="employee-section"><strong>Address:</strong> {{ $emp->current_address ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Extra Mobile:</strong> {{ $emp->extra_mobile ?? 'N/A' }}</div>

        <div class="employee-section"><strong>Aadhar No:</strong> {{ $emp->aadhar_number ?? 'N/A' }}</div>
        <div class="employee-section"><strong>PAN No:</strong> {{ $emp->pan_number ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Driving License:</strong> {{ $emp->driving_license ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Voter ID:</strong> {{ $emp->voter_id ?? 'N/A' }}</div>

        <div class="employee-section"><strong>Bank IFSC:</strong> {{ $emp->bank_ifsc ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Account No:</strong> {{ $emp->bank_account ?? 'N/A' }}</div>
        <div class="employee-section"><strong>Bank Name:</strong> {{ $emp->bank_name ?? 'N/A' }}</div>

        <div class="employee-section employee-doc">
            <strong>Documents:</strong><br>
            @if ($emp->passport_photo)
                <a href="{{ asset('storage/' . $emp->passport_photo) }}" target="_blank">Passport Photo</a>
            @endif
            @if ($emp->aadhar_doc)
                <a href="{{ asset('storage/' . $emp->aadhar_doc) }}" target="_blank">Aadhar</a>
            @endif
            @if ($emp->pan_doc)
                <a href="{{ asset('storage/' . $emp->pan_doc) }}" target="_blank">PAN</a>
            @endif
            @if ($emp->driving_license_doc)
                <a href="{{ asset('storage/' . $emp->driving_license_doc) }}" target="_blank">DL</a>
            @endif
            @if ($emp->voter_id_doc)
                <a href="{{ asset('storage/' . $emp->voter_id_doc) }}" target="_blank">Voter ID</a>
            @endif
            @if ($emp->passbook_image)
                <a href="{{ asset('storage/' . $emp->passbook_image) }}" target="_blank">Passbook</a>
            @endif
            @if ($emp->resume)
                <a href="{{ asset('storage/' . $emp->resume) }}" target="_blank">Resume</a>
            @endif
            @if ($emp->prev_appointment_letter)
                <a href="{{ asset('storage/' . $emp->prev_appointment_letter) }}" target="_blank">Appointment
                    Letter</a>
            @endif
            @if ($emp->prev_offer_letter)
                <a href="{{ asset('storage/' . $emp->prev_offer_letter) }}" target="_blank">Offer Letter</a>
            @endif
            @if ($emp->prev_salary_slips)
                <a href="{{ asset('storage/' . $emp->prev_salary_slips) }}" target="_blank">Salary Slips</a>
            @endif
            @if ($emp->prev_relieving_letter)
                <a href="{{ asset('storage/' . $emp->prev_relieving_letter) }}" target="_blank">Relieving Letter</a>
            @endif
            @if ($emp->form_16)
                <a href="{{ asset('storage/' . $emp->form_16) }}" target="_blank">Form 16</a>
            @endif
        </div>

        <div class="btn-actions">
            <form action="{{ route('employee.toggleStatus', $emp->user->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm {{ $emp->user->is_active ? 'btn-danger' : 'btn-success' }}">
                    <i class="bi bi-{{ $emp->user->is_active ? 'x-circle' : 'check-circle' }}"></i>
                    {{ $emp->user->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            
            <a href="{{ route('employee.edit', $emp->id) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>

            <form action="{{ route('employee.destroy', $emp->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure you want to delete this employee?')">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>

    </div>
@endforeach

@if($employees->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-people fs-1 text-muted"></i>
        <h4 class="text-muted mt-3">No employees found</h4>
        <p class="text-muted">Start by adding your first employee to the system.</p>
        <a href="{{ route('employee.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add First Employee
        </a>
    </div>
@endif

@endsection
