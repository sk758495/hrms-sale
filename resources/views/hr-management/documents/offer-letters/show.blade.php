<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
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
                    <a href="{{ route('offer-letters.index') }}" class="btn btn-outline-light btn-sm">Back</a>
                </div>
            </div>
        </nav>
    </div> --}}

    <div class="container">
        <div class="letter-content">
            <div class="letterhead">
                <h2>OFFER LETTER</h2>
                <p>HR Management System</p>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <strong>Date:</strong> {{ $offerLetter->offer_date->format('d F Y') }}
                </div>
                <div class="col-6 text-end">
                    <strong>Ref:</strong> OL/{{ $offerLetter->id }}/{{ date('Y') }}
                </div>
            </div>

            <div class="mb-4">
                <strong>To,</strong><br>
                {{ $offerLetter->user->name }}<br>
                {{ $offerLetter->employeeData->current_address }}
            </div>

            <div class="mb-4">
                <strong>Subject: Job Offer - {{ $offerLetter->employeeData->position->name ?? 'Position' }}</strong>
            </div>

            <div class="mb-4">
                Dear {{ $offerLetter->user->name }},
            </div>

            <div class="mb-4">
                We are pleased to offer you the position of <strong>{{ $offerLetter->employeeData->position->name ?? 'Employee' }}</strong> 
                in the <strong>{{ $offerLetter->employeeData->department->name ?? 'Department' }}</strong> department of our organization.
            </div>

            <div class="mb-4">
                <strong>Terms of Employment:</strong>
                <ul>
                    <li><strong>Position:</strong> {{ $offerLetter->employeeData->position->name ?? 'N/A' }}</li>
                    <li><strong>Department:</strong> {{ $offerLetter->employeeData->department->name ?? 'N/A' }}</li>
                    <li><strong>Annual Salary:</strong> ₹{{ number_format((float)$offerLetter->offered_salary) }}</li>
                    <li><strong>Probation Period:</strong> {{ $offerLetter->probation_period }}</li>
                    <li><strong>Expected Joining Date:</strong> {{ $offerLetter->joining_date->format('d F Y') }}</li>
                </ul>
            </div>

            @if($offerLetter->job_description)
            <div class="mb-4">
                <strong>Job Description:</strong>
                <div style="white-space: pre-line;">{{ $offerLetter->job_description }}</div>
            </div>
            @endif

            <div class="mb-4">
                This offer is contingent upon successful completion of background verification and reference checks.
            </div>

            <div class="mb-4">
                Please confirm your acceptance of this offer by signing and returning a copy of this letter by {{ $offerLetter->joining_date->subDays(3)->format('d F Y') }}.
            </div>

            <div class="mb-4">
                We look forward to welcoming you to our team.
            </div>

            <div class="row mt-5">
                <div class="col-6">
                    <div class="mb-4">
                        <strong>Candidate Acceptance:</strong><br><br>
                        _________________________<br>
                        {{ $offerLetter->user->name }}<br>
                        Date: _______________
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="mb-4">
                        <strong>HR Signature:</strong><br><br>
                        _________________________<br>
                        {{ $offerLetter->hr->name }}<br>
                        HR Manager<br>
                        Date: {{ now()->format('d F Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>