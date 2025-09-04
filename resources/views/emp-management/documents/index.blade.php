@extends('layouts.employee-app')

@section('title', 'My Documents')

@section('content')
<div class="container-fluid">
    <!-- Employee Information -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-person-circle"></i> {{ $employee->user->name }}</h4>
            <small>{{ $employee->department->name ?? 'N/A' }} - {{ $employee->position->title ?? 'N/A' }}</small>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-primary">Personal</h6>
                    <p><strong>Email:</strong> {{ $employee->user->email }}</p>
                    <p><strong>CTC:</strong> ₹{{ number_format($employee->ctc ?? 0) }}</p>
                    <p><strong>Mobile:</strong> {{ $employee->extra_mobile ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-success">Identity</h6>
                    <p><strong>Aadhar:</strong> {{ $employee->aadhar_number ?? 'N/A' }}</p>
                    <p><strong>PAN:</strong> {{ $employee->pan_number ?? 'N/A' }}</p>
                    <p><strong>Experience:</strong> {{ $employee->experience_type ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-warning">Banking</h6>
                    <p><strong>Bank:</strong> {{ $employee->bank_name ?? 'N/A' }}</p>
                    <p><strong>Account:</strong> {{ $employee->bank_account ?? 'N/A' }}</p>
                    <p><strong>IFSC:</strong> {{ $employee->bank_ifsc ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-info">Address</h6>
                    <p>{{ $employee->current_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- HR Generated Documents -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> HR Generated Documents</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white text-center">
                            <i class="bi bi-file-earmark-check"></i> Appointment Letters
                        </div>
                        <div class="card-body">
                            @forelse($employee->appointmentLetters as $letter)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>{{ $letter->appointment_date->format('d/m/Y') }}</small>
                                <a href="{{ route('emp.documents.download.appointment', $letter->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @empty
                            <p class="text-muted text-center">No documents</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white text-center">
                            <i class="bi bi-file-earmark-plus"></i> Offer Letters
                        </div>
                        <div class="card-body">
                            @forelse($employee->offerLetters as $letter)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>{{ $letter->offer_date->format('d/m/Y') }}</small>
                                <a href="{{ route('emp.documents.download.offer', $letter->id) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @empty
                            <p class="text-muted text-center">No documents</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-dark text-center">
                            <i class="bi bi-shield-lock"></i> NDAs
                        </div>
                        <div class="card-body">
                            @forelse($employee->ndas as $nda)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>{{ $nda->nda_date->format('d/m/Y') }}</small>
                                <a href="{{ route('emp.documents.download.nda', $nda->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @empty
                            <p class="text-muted text-center">No documents</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white text-center">
                            <i class="bi bi-receipt"></i> Salary Slips
                        </div>
                        <div class="card-body">
                            @forelse($employee->salarySlips as $slip)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>{{ $slip->month_year }}</small>
                                <a href="{{ route('emp.documents.download.salary', $slip->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @empty
                            <p class="text-muted text-center">No documents</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded Documents -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> My Uploaded Documents</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-secondary">
                        <div class="card-header bg-secondary text-white text-center">
                            <i class="bi bi-person-vcard"></i> Identity Documents
                        </div>
                        <div class="card-body">
                            @if($employee->aadhar_doc)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Aadhar Card</small>
                                <a href="{{ asset('storage/' . $employee->aadhar_doc) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->pan_doc)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>PAN Card</small>
                                <a href="{{ asset('storage/' . $employee->pan_doc) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->driving_license_doc)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Driving License</small>
                                <a href="{{ asset('storage/' . $employee->driving_license_doc) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->voter_id_doc)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Voter ID</small>
                                <a href="{{ asset('storage/' . $employee->voter_id_doc) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if(!$employee->aadhar_doc && !$employee->pan_doc && !$employee->driving_license_doc && !$employee->voter_id_doc)
                            <p class="text-muted text-center">No documents</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-dark">
                        <div class="card-header bg-dark text-white text-center">
                            <i class="bi bi-file-person"></i> Personal Documents
                        </div>
                        <div class="card-body">
                            @if($employee->passport_photo)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Passport Photo</small>
                                <a href="{{ asset('storage/' . $employee->passport_photo) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->resume)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Resume</small>
                                <a href="{{ asset('storage/' . $employee->resume) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->passbook_image)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Bank Passbook</small>
                                <a href="{{ asset('storage/' . $employee->passbook_image) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if(!$employee->passport_photo && !$employee->resume && !$employee->passbook_image)
                            <p class="text-muted text-center">No documents</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-dark text-center">
                            <i class="bi bi-building"></i> Previous Company
                        </div>
                        <div class="card-body">
                            @if($employee->prev_offer_letter)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Offer Letter</small>
                                <a href="{{ asset('storage/' . $employee->prev_offer_letter) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->prev_appointment_letter)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Appointment Letter</small>
                                <a href="{{ asset('storage/' . $employee->prev_appointment_letter) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->prev_salary_slips)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Salary Slips</small>
                                <a href="{{ asset('storage/' . $employee->prev_salary_slips) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->prev_relieving_letter)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Relieving Letter</small>
                                <a href="{{ asset('storage/' . $employee->prev_relieving_letter) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if($employee->form_16)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                <small>Form 16</small>
                                <a href="{{ asset('storage/' . $employee->form_16) }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            @endif
                            @if(!$employee->prev_offer_letter && !$employee->prev_appointment_letter && !$employee->prev_salary_slips && !$employee->prev_relieving_letter && !$employee->form_16)
                            <p class="text-muted text-center">No documents/Fresher Account</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection