
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>My Employee Data</h4>
                    @if(!$employeeData)
                        <a href="{{ route('user.employee-data.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add Employee Data
                        </a>
                    @else
                        <a href="{{ route('user.employee-data.edit') }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Data
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($employeeData)
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Personal Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td>{{ $employeeData->department->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Position:</strong></td>
                                        <td>{{ $employeeData->position->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $employeeData->current_address }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Extra Mobile:</strong></td>
                                        <td>{{ $employeeData->extra_mobile ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Experience Type:</strong></td>
                                        <td>{{ ucfirst($employeeData->experience_type) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Documents Status</h5>
                                <table class="table table-borderless">
                                    @php
                                        $docs = [
                                            'passport_photo' => 'Passport Photo',
                                            'aadhar_doc' => 'Aadhar',
                                            'pan_doc' => 'PAN',
                                            'driving_license_doc' => 'Driving License',
                                            'voter_id_doc' => 'Voter ID',
                                            'passbook_image' => 'Bank Passbook',
                                            'resume' => 'Resume'
                                        ];
                                        if($employeeData->experience_type == 'experienced') {
                                            $docs = array_merge($docs, [
                                                'prev_offer_letter' => 'Previous Offer Letter',
                                                'prev_appointment_letter' => 'Previous Appointment Letter',
                                                'prev_salary_slips' => 'Previous Salary Slips',
                                                'prev_relieving_letter' => 'Previous Relieving Letter',
                                                'form_16' => 'Form 16'
                                            ]);
                                        }
                                    @endphp
                                    @foreach($docs as $field => $label)
                                    <tr>
                                        <td><strong>{{ $label }}:</strong></td>
                                        <td>
                                            @if($employeeData->$field)
                                                @php $statusField = $field . '_status'; @endphp
                                                <span class="badge bg-{{ $employeeData->$statusField == 'approved' ? 'success' : ($employeeData->$statusField == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($employeeData->$statusField) }}
                                                </span>
                                                @php $remarksField = $field . '_remarks'; @endphp
                                                @if($employeeData->$statusField == 'rejected' && $employeeData->$remarksField)
                                                    <br><small class="text-danger">{{ $employeeData->$remarksField }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Not uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        @php
                            $rejectedDocs = [];
                            foreach($docs as $field => $label) {
                                $statusField = $field . '_status';
                                if($employeeData->$statusField == 'rejected') {
                                    $rejectedDocs[$field] = $label;
                                }
                            }
                        @endphp
                        
                        @if(!empty($rejectedDocs))
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5>Rejected Documents - Re-upload Required</h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($rejectedDocs as $field => $label)
                                        <div class="mb-3 p-3 border border-danger rounded">
                                            <h6 class="text-danger">{{ $label }}</h6>
                                            @php $remarksField = $field . '_remarks'; @endphp
                                            @if($employeeData->$remarksField)
                                                <p class="text-danger mb-2"><strong>Reason:</strong> {{ $employeeData->$remarksField }}</p>
                                            @endif
                                            <form action="{{ route('user.employee-data.reupload-document') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="document_type" value="{{ $field }}">
                                                <div class="input-group">
                                                    <input type="file" name="document" class="form-control" required>
                                                    <button type="submit" class="btn btn-primary">Re-upload</button>
                                                </div>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center">
                            <p>No employee data found. Please add your employee information.</p>
                            <a href="{{ route('user.employee-data.create') }}" class="btn btn-primary">Add Employee Data</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
