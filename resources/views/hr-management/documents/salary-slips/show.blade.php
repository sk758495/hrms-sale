<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .salary-slip {
            background: white;
            padding: 30px;
            margin: 20px 0;
            border: 2px solid #000;
            font-family: Arial, sans-serif;
        }
        .company-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .employee-info {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }
        .salary-table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #000;
        }
        .salary-table th, .salary-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .salary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    {{-- <div class="no-print">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="{{ route('hr.dashboard') }}">🏢 HR System</a>
                <div class="navbar-nav ms-auto">
                    <button onclick="window.print()" class="btn btn-outline-light btn-sm me-2">Print</button>
                    <a href="{{ route('salary-slips.index') }}" class="btn btn-outline-light btn-sm">Back</a>
                </div>
            </div>
        </nav>
    </div> --}}

    <div class="container">
        <div class="salary-slip">
            <div class="company-header">
                <h3>SALARY SLIP</h3>
                <p><strong>HR Management System</strong></p>
                <p>Month: {{ $salarySlip->month_year }}</p>
            </div>

            <div class="employee-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Employee Name:</strong> {{ $salarySlip->user->name }}<br>
                        <strong>Employee ID:</strong> {{ $salarySlip->user->employee_id }}<br>
                        <strong>Department:</strong> {{ $salarySlip->employeeData->department->name ?? 'N/A' }}<br>
                        <strong>Position:</strong> {{ $salarySlip->employeeData->position->name ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>PAN:</strong> {{ $salarySlip->employeeData->pan_number ?? 'N/A' }}<br>
                        <strong>Bank Account:</strong> {{ $salarySlip->employeeData->bank_account ?? 'N/A' }}<br>
                        <strong>Bank Name:</strong> {{ $salarySlip->employeeData->bank_name ?? 'N/A' }}<br>
                        <strong>IFSC:</strong> {{ $salarySlip->employeeData->bank_ifsc ?? 'N/A' }}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Date of Joining:</strong> {{ $salarySlip->joining_date->format('d M Y') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Date:</strong> {{ $salarySlip->payment_date->format('d M Y') }}
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Present Days:</strong> {{ $salarySlip->present_days }}
                </div>
                <div class="col-md-4">
                    <strong>Leave Taken:</strong> {{ $salarySlip->leave_taken }}
                </div>
                <div class="col-md-4">
                    <strong>Balance Leave:</strong> {{ $salarySlip->balance_leave }}
                </div>
            </div>

            <table class="salary-table">
                <thead>
                    <tr>
                        <th width="50%">EARNINGS</th>
                        <th width="20%">AMOUNT (₹)</th>
                        <th width="30%">DEDUCTIONS</th>
                        <th width="20%">AMOUNT (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td>{{ number_format((float)$salarySlip->basic_salary, 2) }}</td>
                        <td>Professional Tax</td>
                        <td>{{ number_format((float)$salarySlip->professional_tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>HRA</td>
                        <td>{{ number_format((float)$salarySlip->hra, 2) }}</td>
                        <td>Advance Pay</td>
                        <td>{{ number_format((float)$salarySlip->advance_pay, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Traveling Allowance</td>
                        <td>{{ number_format((float)$salarySlip->traveling_allowance, 2) }}</td>
                        <td>Arrears Deductions</td>
                        <td>{{ number_format((float)$salarySlip->arrears_deductions, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Other Allowances</td>
                        <td>{{ number_format((float)$salarySlip->other_allowances, 2) }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Miscellaneous</td>
                        <td>{{ number_format((float)$salarySlip->miscellaneous, 2) }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>TOTAL EARNINGS</strong></td>
                        <td><strong>{{ number_format((float)$salarySlip->total_earnings, 2) }}</strong></td>
                        <td><strong>TOTAL DEDUCTIONS</strong></td>
                        <td><strong>{{ number_format((float)$salarySlip->total_deductions, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div style="border: 2px solid #000; padding: 15px; text-align: center; background-color: #f0f0f0;">
                        <h4><strong>NET SALARY: ₹{{ number_format((float)$salarySlip->net_salary, 2) }}</strong></h4>
                        <p><em>In Words: {{ \App\Helpers\NumberToWords::convert($salarySlip->net_salary) }} Rupees Only</em></p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <strong>Employee Signature:</strong><br><br>
                    _________________________<br>
                    {{ $salarySlip->user->name }}
                </div>
                <div class="col-md-6 text-end">
                    <strong>HR Signature:</strong><br><br>
                    _________________________<br>
                    {{ $salarySlip->hr->name }}<br>
                    HR Manager
                </div>
            </div>

            <div class="mt-4 text-center">
                <small><em>This is a computer-generated salary slip and does not require a signature.</em></small>
            </div>
        </div>
    </div>
</body>
</html>