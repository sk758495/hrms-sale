<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Letter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .letter-content {
            background: white;
            padding: 40px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }
        .letterhead {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
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
                    <a href="{{ route('appointment-letters.index') }}" class="btn btn-outline-light btn-sm">Back</a>
                </div>
            </div>
        </nav>
    </div> --}}

    <div class="container">
        <div class="letter-content">
            <div class="letterhead">
                <h2>APPOINTMENT LETTER</h2>
                <p>HR Management System</p>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <strong>Date:</strong> {{ $appointmentLetter->appointment_date->format('d F Y') }}
                </div>
                <div class="col-6 text-end">
                    <strong>Ref:</strong> AL/{{ $appointmentLetter->id }}/{{ date('Y') }}
                </div>
            </div>

            <div class="mb-4">
                <strong>To,</strong><br>
                {{ $appointmentLetter->user->name }}<br>
                {{ $appointmentLetter->employeeData->current_address }}
            </div>

            <div class="mb-4">
                <strong>Subject: Appointment as {{ $appointmentLetter->employeeData->position->name ?? 'Employee' }}</strong>
            </div>

            <div class="mb-4">
                Dear {{ $appointmentLetter->user->name }},
            </div>

            <div class="mb-4">
                We are pleased to inform you that you have been appointed as <strong>{{ $appointmentLetter->employeeData->position->name ?? 'Employee' }}</strong> 
                in the <strong>{{ $appointmentLetter->employeeData->department->name ?? 'Department' }}</strong> department of our organization.
            </div>

            <div class="mb-4">
                <strong>Details of your appointment:</strong>
                <ul>
                    <li><strong>Employee ID:</strong> {{ $appointmentLetter->user->employee_id }}</li>
                    <li><strong>Position:</strong> {{ $appointmentLetter->employeeData->position->name ?? 'N/A' }}</li>
                    <li><strong>Department:</strong> {{ $appointmentLetter->employeeData->department->name ?? 'N/A' }}</li>
                    <li><strong>CTC:</strong> ₹{{ number_format((float)str_replace(',', '', $appointmentLetter->employeeData->ctc ?? 0)) }} per annum</li>
                    <li><strong>Date of Joining:</strong> {{ $appointmentLetter->joining_date->format('d F Y') }}</li>
                </ul>
            </div>

            @if($appointmentLetter->terms_conditions)
            <div class="mb-4">
                <strong>Terms & Conditions:</strong>
                <div style="white-space: pre-line;">{{ $appointmentLetter->terms_conditions }}</div>
            </div>
            @endif

            <div class="mb-4">
                Please confirm your acceptance of this appointment by signing and returning a copy of this letter.
            </div>

            <div class="mb-4">
                We look forward to your valuable contribution to our organization.
            </div>

            <div class="row mt-5">
                <div class="col-6">
                    <div class="mb-4">
                        <strong>Employee Signature:</strong><br><br>
                        _________________________<br>
                        {{ $appointmentLetter->user->name }}<br>
                        Date: _______________
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="mb-4">
                        <strong>HR Signature:</strong><br><br>
                        _________________________<br>
                        {{ $appointmentLetter->hr->name }}<br>
                        HR Manager<br>
                        Date: {{ now()->format('d F Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>