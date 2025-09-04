<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Salary Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('hr.dashboard') }}">🏢 HR System</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-receipt"></i> Generate Salary Slip</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('salary-slips.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Select Employee</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="month_year" class="form-label">Month & Year</label>
                                <input type="month" name="month_year" id="month_year" class="form-control" required>
                                @error('month_year')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="employee-details" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Present Days</label>
                                    <input type="number" name="present_days" id="present_days" class="form-control" min="0" max="31" required>
                                    <small class="text-muted">Maximum days for selected month</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Leave Taken (Auto)</label>
                                    <input type="number" name="leave_taken" class="form-control" value="0" required readonly>
                                    <small class="text-muted">Month Days - Present Days</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Balance Leave</label>
                                    <input type="number" name="balance_leave" class="form-control" value="0" required>
                                </div>
                            </div>
                        </div>

                        <h5>Earnings</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Basic Salary</label>
                                    <input type="number" name="basic_salary" id="basic_salary" class="form-control" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">HRA</label>
                                    <input type="number" name="hra" id="hra" class="form-control" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Traveling Allowance</label>
                                    <input type="number" name="traveling_allowance" id="traveling_allowance" class="form-control" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Other Allowances</label>
                                    <input type="number" name="other_allowances" id="other_allowances" class="form-control" required readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Miscellaneous (Manual)</label>
                                    <input type="number" name="miscellaneous" id="miscellaneous" class="form-control" value="0" required>
                                    <small class="text-muted">Enter any additional amount manually</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Expected Salary (Reference)</label>
                                    <input type="number" id="gross_salary" class="form-control" readonly style="background-color: #e3f2fd;">
                                    <small class="text-muted">Monthly Salary × Present Days ÷ Month Days</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Final Net Salary</label>
                                    <input type="number" id="final_net_salary" class="form-control" readonly style="background-color: #d4edda;">
                                    <small class="text-muted">Total Earnings - Deductions</small>
                                </div>
                            </div>
                        </div>

                        <h5>Deductions</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Professional Tax</label>
                                    <input type="number" name="professional_tax" id="professional_tax" class="form-control" value="200" required readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Advance Pay</label>
                                    <input type="number" name="advance_pay" class="form-control" value="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Arrears Deductions</label>
                                    <input type="number" name="arrears_deductions" class="form-control" value="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('salary-slips.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Generate Salary Slip</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let employeeCTC = 0;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Employee selection handler
            document.getElementById('user_id').addEventListener('change', function() {
                const userId = this.value;
                const detailsDiv = document.getElementById('employee-details');
                
                if (userId) {
                    console.log('Fetching data for user:', userId);
                    fetch('/salary-slips/employee-data?user_id=' + userId)
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Employee data received:', data);
                            employeeCTC = parseFloat(data.ctc) || 0;
                            console.log('Parsed CTC:', employeeCTC);
                            
                            document.getElementById('professional_tax').value = 200;
                            detailsDiv.style.display = 'block';
                            calculateSalary();
                        })
                        .catch(error => {
                            console.error('Error fetching employee data:', error);
                            alert('Error fetching employee data. Please try again.');
                        });
                } else {
                    detailsDiv.style.display = 'none';
                    employeeCTC = 0;
                }
            });

            // Present days input handler
            document.getElementById('present_days').addEventListener('input', function() {
                const monthDays = getMonthDays();
                const presentDays = parseInt(this.value) || 0;
                
                // Validate present days doesn't exceed month days
                if (presentDays > monthDays) {
                    alert(`Present days cannot exceed ${monthDays} days for this month!`);
                    this.value = monthDays;
                }
                
                console.log('Present days changed:', this.value);
                calculateLeave();
                calculateSalary();
            });

            // Month change handler
            document.getElementById('month_year').addEventListener('change', function() {
                console.log('Month changed:', this.value);
                
                // Update max attribute for present days
                const monthDays = getMonthDays();
                document.getElementById('present_days').setAttribute('max', monthDays);
                
                calculateLeave();
                calculateSalary();
                checkExistingSalarySlip();
            });

            // Miscellaneous input handler
            document.getElementById('miscellaneous').addEventListener('input', function() {
                console.log('Miscellaneous changed:', this.value);
                calculateSalary();
            });
        });

        function getMonthDays() {
            const monthYear = document.getElementById('month_year').value;
            if (monthYear) {
                const [year, month] = monthYear.split('-');
                const days = new Date(year, month, 0).getDate();
                console.log('Month days for', monthYear, ':', days);
                return days;
            }
            return 30;
        }

        function calculateLeave() {
            const monthDays = getMonthDays();
            const presentDays = parseInt(document.getElementById('present_days').value) || 0;
            
            if (presentDays > 0 && monthDays > 0) {
                const leaveTaken = monthDays - presentDays;
                document.querySelector('input[name="leave_taken"]').value = Math.max(0, leaveTaken);
                console.log('Auto-calculated leave:', leaveTaken, '(Month days:', monthDays, '- Present days:', presentDays, ')');
            }
        }

        function calculateSalary() {
            const monthDays = getMonthDays();
            const presentDays = parseInt(document.getElementById('present_days').value) || 0;
            
            console.log('Calculating salary with CTC:', employeeCTC, 'Month Days:', monthDays, 'Present Days:', presentDays);
            
            if (employeeCTC > 0 && presentDays > 0 && monthDays > 0) {
                // 1. Monthly Salary from CTC
                const monthlySalary = employeeCTC / 12;
                console.log('Monthly salary:', monthlySalary);
                
                // 2. Expected Salary = (monthly salary * present days) / total working days
                const expectedSalary = (monthlySalary * presentDays) / monthDays;
                console.log('Expected salary:', expectedSalary);
                
                // 3. Basic Salary = (40% of monthly salary / total working days) * present days
                const basicSalary = Math.round((monthlySalary * 0.4 / monthDays) * presentDays);
                
                // 4. HRA = (40% of basic salary / total working days) * present days
                const hra = Math.round((basicSalary * 0.4 / monthDays) * presentDays);
                
                // 5. Travelling Allowance = (1600 / total working days) * present days
                const travelingAllowance = Math.round((1600 / monthDays) * presentDays);
                
                // 6. Other Allowances = Expected Salary - (Basic + HRA + Travelling)
                const otherAllowances = Math.round(expectedSalary - basicSalary - hra - travelingAllowance);
                
                // 7. Get manual miscellaneous value (this will be ADDED to total)
                const miscellaneous = parseFloat(document.getElementById('miscellaneous').value) || 0;
                
                console.log('Calculated values:', {
                    basicSalary, hra, travelingAllowance, otherAllowances, miscellaneous
                });
                
                // Calculate final totals for display
                const totalEarnings = basicSalary + hra + travelingAllowance + otherAllowances + miscellaneous;
                const totalDeductions = 200; // Professional tax
                const netSalary = totalEarnings - totalDeductions;
                
                console.log('Final totals:', {
                    totalEarnings, totalDeductions, netSalary
                });
                
                // Update form fields
                document.getElementById('basic_salary').value = basicSalary;
                document.getElementById('hra').value = hra;
                document.getElementById('traveling_allowance').value = travelingAllowance;
                document.getElementById('other_allowances').value = otherAllowances;
                document.getElementById('gross_salary').value = Math.round(expectedSalary);
                document.getElementById('final_net_salary').value = Math.round(netSalary);
            } else {
                console.log('Cannot calculate - missing data');
            }
        }

        function checkExistingSalarySlip() {
            const userId = document.getElementById('user_id').value;
            const monthYear = document.getElementById('month_year').value;
            
            if (userId && monthYear) {
                fetch('/salary-slips/check-existing?user_id=' + userId + '&month_year=' + monthYear)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alert('Salary slip for this month already exists for this employee!');
                            document.getElementById('month_year').value = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking existing salary slip:', error);
                    });
            }
        }
    </script>
</body>
</html>