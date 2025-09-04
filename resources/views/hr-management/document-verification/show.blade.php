@extends('layouts.hr-app')

@section('title', 'Document Verification - ' . $employee->user->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-file-check"></i> Document Verification - {{ $employee->user->name }}</h4>
                </div>
                <div class="card-body">
                    @php
                        $documents = [
                            'passport_photo' => 'Passport Photo',
                            'aadhar_doc' => 'Aadhar Document',
                            'pan_doc' => 'PAN Document',
                            'driving_license_doc' => 'Driving License',
                            'voter_id_doc' => 'Voter ID',
                            'passbook_image' => 'Bank Passbook',
                            'resume' => 'Resume',
                            'prev_offer_letter' => 'Previous Offer Letter',
                            'prev_appointment_letter' => 'Previous Appointment Letter',
                            'prev_salary_slips' => 'Previous Salary Slips',
                            'prev_relieving_letter' => 'Previous Relieving Letter',
                            'form_16' => 'Form 16'
                        ];
                    @endphp

                    @foreach($documents as $field => $label)
                        @php
                            $experienceFields = ['prev_offer_letter', 'prev_appointment_letter', 'prev_salary_slips', 'prev_relieving_letter', 'form_16'];
                            $isExperienceDoc = in_array($field, $experienceFields);
                            $showDocument = $employee->$field || ($isExperienceDoc && $employee->experience_type == 'experienced');
                        @endphp
                        @if($showDocument && (!$isExperienceDoc || $employee->experience_type == 'experienced'))
                        <div class="document-item mb-4 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>{{ $label }}</h6>
                                    @if($employee->$field)
                                        <a href="{{ route('hr.document-verification.download-document', [$employee, $field]) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-download"></i> Download Document
                                        </a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @php $statusField = $field . '_status'; @endphp
                                    <div class="verification-status">
                                        <span class="badge bg-{{ $employee->$statusField == 'approved' ? 'success' : ($employee->$statusField == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($employee->$statusField) }}
                                        </span>
                                        
                                        @if($employee->$statusField == 'pending')
                                        <div class="mt-2">
                                            @if($employee->$field)
                                                <button class="btn btn-success btn-sm" onclick="console.log('Approving {{ $field }}'); verifyDocument('{{ $field }}', 'approved')">
                                                    <i class="bi bi-check"></i> Approve
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="showRejectModal('{{ $field }}', '{{ $label }}')">
                                                    <i class="bi bi-x"></i> Reject
                                                </button>
                                            @else
                                                <small class="text-warning">Document not uploaded yet</small>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        @php $remarksField = $field . '_remarks'; @endphp
                                        @if($employee->$remarksField)
                                        <div class="mt-2">
                                            <small class="text-muted">Remarks: {{ $employee->$remarksField }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Employee Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $employee->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $employee->user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Position:</strong></td>
                            <td>{{ $employee->position->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Experience:</strong></td>
                            <td>{{ ucfirst($employee->experience_type) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($employee->overall_status == 'under_review' && $allDocumentsApproved)
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Final Actions</h5>
                </div>
                <div class="card-body">
                    @if(!$employee->ctc)
                        <form action="{{ route('hr.document-verification.add-ctc', $employee) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">CTC Amount</label>
                                <input type="number" name="ctc" class="form-control" placeholder="Enter CTC" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-currency-rupee"></i> Add CTC
                            </button>
                        </form>
                    @else
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>CTC Added:</strong> ₹{{ number_format($employee->ctc) }}
                            </div>
                        </div>
                        <form action="{{ route('hr.document-verification.final-approval', $employee) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Final Approval
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @elseif($employee->overall_status == 'under_review' && !$allDocumentsApproved)
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Pending Actions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        All documents must be approved before final actions can be taken.
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($pendingDocuments))
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Remaining Documents ({{ count($pendingDocuments) }})</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($pendingDocuments as $document)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $document }}
                            <span class="badge bg-warning rounded-pill">Pending</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Document: <span id="documentName"></span></label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rejection Remarks</label>
                    <textarea id="rejectionRemarks" class="form-control" rows="3" placeholder="Enter reason for rejection"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject Document</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDocumentType = '';

function verifyDocument(documentType, status, remarks = '') {
    console.log('Verifying document:', documentType, 'Status:', status);
    
    fetch(`{{ route('hr.document-verification.verify-document', $employee) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            document_type: documentType,
            status: status,
            remarks: remarks
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Document status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Network error: ' + error.message);
    });
}

function showRejectModal(documentType, documentName) {
    currentDocumentType = documentType;
    document.getElementById('documentName').textContent = documentName;
    document.getElementById('rejectionRemarks').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function confirmReject() {
    const remarks = document.getElementById('rejectionRemarks').value;
    if (remarks.trim() === '') {
        alert('Please enter rejection remarks');
        return;
    }
    
    verifyDocument(currentDocumentType, 'rejected', remarks);
    bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
}
</script>
@endsection