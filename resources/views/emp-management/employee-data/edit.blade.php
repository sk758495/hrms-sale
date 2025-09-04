
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Edit My Employee Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('user.employee-data.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')



                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="departmentSelect" class="form-label">Department</label>
                        <select name="department_id" id="departmentSelect" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $employeeData->department_id == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="positionSelect" class="form-label">Position</label>
                        <select name="position_id" id="positionSelect" class="form-select">
                            <option value="">Select Position</option>
                            @foreach ($positions as $pos)
                                <option value="{{ $pos->id }}" {{ $employeeData->position_id == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Passport Photo</label>
                        <input type="file" name="passport_photo" class="form-control">
                        @if($employeeData->passport_photo)
                            <small class="text-success">✓ File uploaded</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Address</label>
                        <textarea name="current_address" class="form-control" rows="2" required>{{ $employeeData->current_address }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" name="extra_mobile" class="form-control" placeholder="Extra Mobile" value="{{ $employeeData->extra_mobile }}">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="aadhar_number" class="form-control" placeholder="Aadhar Number" value="{{ $employeeData->aadhar_number }}">
                        <input type="file" name="aadhar_doc" class="form-control mt-1">
                        @if($employeeData->aadhar_doc)
                            <small class="text-success">✓ File uploaded</small>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" name="pan_number" class="form-control" placeholder="PAN Number" value="{{ $employeeData->pan_number }}">
                        <input type="file" name="pan_doc" class="form-control mt-1">
                        @if($employeeData->pan_doc)
                            <small class="text-success">✓ File uploaded</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="driving_license" class="form-control" placeholder="Driving License" value="{{ $employeeData->driving_license }}">
                        <input type="file" name="driving_license_doc" class="form-control mt-1">
                        @if($employeeData->driving_license_doc)
                            <small class="text-success">✓ File uploaded</small>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" name="voter_id" class="form-control" placeholder="Voter ID" value="{{ $employeeData->voter_id }}">
                        <input type="file" name="voter_id_doc" class="form-control mt-1">
                        @if($employeeData->voter_id_doc)
                            <small class="text-success">✓ File uploaded</small>
                        @endif
                    </div>

                </div>

                <h6 class="mt-4">Bank Details</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" name="bank_ifsc" class="form-control" placeholder="IFSC" value="{{ $employeeData->bank_ifsc }}">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="bank_account" class="form-control" placeholder="Account Number" value="{{ $employeeData->bank_account }}">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="bank_name" class="form-control" placeholder="Name as per Bank" value="{{ $employeeData->bank_name }}">
                    </div>
                    <div class="col-md-12 mt-2">
                        <input type="file" name="passbook_image" class="form-control">
                        @if($employeeData->passbook_image)
                            <small class="text-success">✓ File uploaded</small>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Resume</label>
                    <input type="file" name="resume" class="form-control">
                    @if($employeeData->resume)
                        <small class="text-success">✓ File uploaded</small>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Experience Type</label>
                    <select name="experience_type" id="experience_type" class="form-select" onchange="toggleExperienceFields()">
                        <option value="fresher" {{ $employeeData->experience_type == 'fresher' ? 'selected' : '' }}>Fresher</option>
                        <option value="experience" {{ $employeeData->experience_type == 'experience' ? 'selected' : '' }}>Experience</option>
                    </select>
                </div>

                <div id="experience_fields" class="mb-3" style="display: {{ $employeeData->experience_type == 'experience' ? 'block' : 'none' }};">
                    <h6>Previous Experience Documents</h6>
                    <input type="file" name="prev_offer_letter" class="form-control mb-2">
                    @if($employeeData->prev_offer_letter)<small class="text-success">✓ File uploaded</small>@endif
                    <input type="file" name="prev_appointment_letter" class="form-control mb-2">
                    @if($employeeData->prev_appointment_letter)<small class="text-success">✓ File uploaded</small>@endif
                    <input type="file" name="prev_salary_slips" class="form-control mb-2">
                    @if($employeeData->prev_salary_slips)<small class="text-success">✓ File uploaded</small>@endif
                    <input type="file" name="prev_relieving_letter" class="form-control mb-2">
                    @if($employeeData->prev_relieving_letter)<small class="text-success">✓ File uploaded</small>@endif
                    <input type="file" name="form_16" class="form-control">
                    @if($employeeData->form_16)<small class="text-success">✓ File uploaded</small>@endif
                </div>

                <div class="text-end">
                    <a href="{{ route('user.employee-data.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleExperienceFields() {
        const value = document.getElementById('experience_type').value;
        document.getElementById('experience_fields').style.display = value === 'experience' ? 'block' : 'none';
    }

    document.getElementById('departmentSelect').addEventListener('change', function() {
        let deptId = this.value;
        fetch(`/my-data/positions/${deptId}`)
            .then(response => response.json())
            .then(data => {
                let positionSelect = document.getElementById('positionSelect');
                positionSelect.innerHTML = '<option value="">Select Position</option>';
                data.forEach(pos => {
                    positionSelect.innerHTML += `<option value="${pos.id}">${pos.name}</option>`;
                });
            });
    });
</script>