@extends('layouts.hr-app')

@section('title', 'Interviews Management')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Add Employee Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Select User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="departmentSelect" class="form-label">Department</label>
                            <select name="department_id" id="departmentSelect" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="positionSelect" class="form-label">Position</label>
                        <select name="position_id" id="positionSelect" class="form-select" required>
                            <option value="">Select Position</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Passport Photo</label>
                            <input type="file" name="passport_photo" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Address</label>
                            <textarea name="current_address" class="form-control" rows="2" placeholder="Current Address" required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="extra_mobile" class="form-control" placeholder="Extra Mobile">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="aadhar_number" class="form-control" placeholder="Aadhar Number">
                            <input type="file" name="aadhar_doc" class="form-control mt-1">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="pan_number" class="form-control" placeholder="PAN Number">
                            <input type="file" name="pan_doc" class="form-control mt-1">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="driving_license" class="form-control"
                                placeholder="Driving License">
                            <input type="file" name="driving_license_doc" class="form-control mt-1">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="voter_id" class="form-control" placeholder="Voter ID">
                            <input type="file" name="voter_id_doc" class="form-control mt-1">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="ctc" class="form-control" placeholder="CTC">
                        </div>
                    </div>

                    <h6 class="mt-4">Bank Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" name="bank_ifsc" class="form-control" placeholder="IFSC">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="bank_account" class="form-control"
                                placeholder="Account Number">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="bank_name" class="form-control"
                                placeholder="Name as per Bank">
                        </div>
                        <div class="col-md-12 mt-2">
                            <input type="file" name="passbook_image" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Resume</label>
                        <input type="file" name="resume" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Experience Type</label>
                        <select name="experience_type" id="experience_type" class="form-select"
                            onchange="toggleExperienceFields()">
                            <option value="fresher">Fresher</option>
                            <option value="experience">Experience</option>
                        </select>
                    </div>

                    <div id="experience_fields" class="mb-3" style="display: none;">
                        <h6>Previous Experience Documents</h6>
                        <input type="file" name="prev_offer_letter" class="form-control mb-2">
                        <input type="file" name="prev_appointment_letter" class="form-control mb-2">
                        <input type="file" name="prev_salary_slips" class="form-control mb-2">
                        <input type="file" name="prev_relieving_letter" class="form-control mb-2">
                        <input type="file" name="form_16" class="form-control">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Submit</button>
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
            fetch(`/positions/by-department/${deptId}`)
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
    
    
@endsection