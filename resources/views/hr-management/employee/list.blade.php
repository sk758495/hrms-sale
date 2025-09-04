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

@include('hr-management.employee.index')
@endsection