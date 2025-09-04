
<div class="container">
    <h2 class="mb-4">Edit Employee Data</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">

            <div class="col-md-6">
                <label for="user_id" class="form-label">Select User</label>
                <select name="user_id" class="form-control" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $employee->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="current_address" class="form-label">Current Address</label>
                <input type="text" name="current_address" class="form-control" value="{{ $employee->current_address }}" required>
            </div>

            <div class="col-md-6">
                <label for="extra_mobile" class="form-label">Extra Mobile</label>
                <input type="text" name="extra_mobile" class="form-control" value="{{ $employee->extra_mobile }}">
            </div>

            <div class="col-md-6">
                <label for="aadhar_number" class="form-label">Aadhar Number</label>
                <input type="text" name="aadhar_number" class="form-control" value="{{ $employee->aadhar_number }}">
            </div>

            <div class="col-md-6">
                <label for="pan_number" class="form-label">PAN Number</label>
                <input type="text" name="pan_number" class="form-control" value="{{ $employee->pan_number }}">
            </div>

            <div class="col-md-6">
                <label for="driving_license" class="form-label">Driving License</label>
                <input type="text" name="driving_license" class="form-control" value="{{ $employee->driving_license }}">
            </div>

            <div class="col-md-6">
                <label for="voter_id" class="form-label">Voter ID</label>
                <input type="text" name="voter_id" class="form-control" value="{{ $employee->voter_id }}">
            </div>

            <div class="col-md-6">
                <label for="ctc" class="form-label">CTC</label>
                <input type="text" name="ctc" class="form-control" value="{{ $employee->ctc }}">
            </div>

            <div class="col-md-6">
                <label for="department_id" class="form-label">Department</label>
                <select name="department_id" id="departmentSelect" class="form-control" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="position_id" class="form-label">Position</label>
                <select name="position_id" id="positionSelect" class="form-control">
                    <option value="">Select Position</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" 
                            data-department="{{ $pos->department_id }}"
                            {{ $employee->position_id == $pos->id ? 'selected' : '' }}
                            style="{{ $employee->department_id != $pos->department_id ? 'display:none;' : '' }}">
                            {{ $pos->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="bank_name" class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="{{ $employee->bank_name }}">
            </div>

            <div class="col-md-6">
                <label for="bank_ifsc" class="form-label">Bank IFSC</label>
                <input type="text" name="bank_ifsc" class="form-control" value="{{ $employee->bank_ifsc }}">
            </div>

            <div class="col-md-6">
                <label for="bank_account" class="form-label">Bank Account</label>
                <input type="text" name="bank_account" class="form-control" value="{{ $employee->bank_account }}">
            </div>

            <div class="col-md-6">
                <label for="experience_type" class="form-label">Experience Type</label>
                <select name="experience_type" class="form-control" required>
                    <option value="fresher" {{ $employee->experience_type === 'fresher' ? 'selected' : '' }}>Fresher</option>
                    <option value="experience" {{ $employee->experience_type === 'experience' ? 'selected' : '' }}>Experience</option>
                </select>
            </div>

            {{-- File Inputs --}}
            @php
                $files = [
                    'passport_photo' => 'Passport Photo',
                    'aadhar_doc' => 'Aadhar Document',
                    'pan_doc' => 'PAN Document',
                    'driving_license_doc' => 'Driving License Document',
                    'voter_id_doc' => 'Voter ID Document',
                    'passbook_image' => 'Passbook Image',
                    'resume' => 'Resume',
                    'prev_offer_letter' => 'Previous Offer Letter',
                    'prev_appointment_letter' => 'Previous Appointment Letter',
                    'prev_salary_slips' => 'Previous Salary Slips',
                    'prev_relieving_letter' => 'Previous Relieving Letter',
                    'form_16' => 'Form 16'
                ];
            @endphp

            @foreach($files as $field => $label)
                <div class="col-md-6">
                    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                    <input type="file" name="{{ $field }}" class="form-control">
                    @if($employee->$field)
                        <small class="text-muted">Current: <a href="{{ asset('storage/' . $employee->$field) }}" target="_blank">View</a></small>
                    @endif
                </div>
            @endforeach

        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Update Employee</button>
            <a href="{{ route('employee-data.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('departmentSelect').addEventListener('change', function () {
        let deptId = this.value;
        let positionSelect = document.getElementById('positionSelect');
        
        // Hide all options first
        Array.from(positionSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            option.style.display = option.dataset.department == deptId ? 'block' : 'none';
        });
        
        // Reset selection
        positionSelect.value = '';
        
        // If no department selected, fetch via API
        if (deptId) {
            fetch(`/positions/by-department/${deptId}`)
                .then(response => response.json())
                .then(data => {
                    positionSelect.innerHTML = '<option value="">Select Position</option>';
                    data.forEach(pos => {
                        positionSelect.innerHTML += `<option value="${pos.id}" data-department="${pos.department_id}">${pos.name}</option>`;
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>