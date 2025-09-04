<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Disclosure Agreement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nda-content {
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
                    <a href="{{ route('ndas.index') }}" class="btn btn-outline-light btn-sm">Back</a>
                </div>
            </div>
        </nav>
    </div> --}}

    <div class="container">
        <div class="nda-content">
            <div class="letterhead">
                <h2>NON-DISCLOSURE AGREEMENT</h2>
                <p>HR Management System</p>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <strong>Date:</strong> {{ $nda->nda_date->format('d F Y') }}
                </div>
                <div class="col-6 text-end">
                    <strong>Ref:</strong> NDA/{{ $nda->id }}/{{ date('Y') }}
                </div>
            </div>

            <div class="mb-4">
                <strong>Employee Details:</strong><br>
                <strong>Name:</strong> {{ $nda->user->name }}<br>
                <strong>Employee ID:</strong> {{ $nda->user->employee_id }}<br>
                <strong>Department:</strong> {{ $nda->employeeData->department->name ?? 'N/A' }}<br>
                <strong>Position:</strong> {{ $nda->employeeData->position->name ?? 'N/A' }}
            </div>

            <div class="mb-4">
                <strong>CONFIDENTIALITY AGREEMENT</strong>
            </div>

            <div class="mb-4">
                This Non-Disclosure Agreement ("Agreement") is entered into between the Company and {{ $nda->user->name }} ("Employee").
            </div>

            <div class="mb-4">
                <strong>Terms and Conditions:</strong>
                <div style="white-space: pre-line; margin-top: 10px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">{{ $nda->confidentiality_terms }}</div>
            </div>

            <div class="mb-4">
                <strong>Validity:</strong> 
                @if($nda->validity_until)
                    This agreement is valid until {{ $nda->validity_until->format('d F Y') }}.
                @else
                    This agreement remains valid indefinitely.
                @endif
            </div>

            <div class="mb-4">
                <strong>Legal Binding:</strong> This agreement is legally binding and any violation may result in legal action and termination of employment.
            </div>

            <div class="mb-4">
                By signing below, both parties acknowledge that they have read, understood, and agree to be bound by the terms of this Agreement.
            </div>

            <div class="row mt-5">
                <div class="col-6">
                    <div class="mb-4">
                        <strong>Employee Signature:</strong><br><br>
                        _________________________<br>
                        {{ $nda->user->name }}<br>
                        Date: _______________
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="mb-4">
                        <strong>Company Representative:</strong><br><br>
                        _________________________<br>
                        {{ $nda->hr->name }}<br>
                        HR Manager<br>
                        Date: {{ now()->format('d F Y') }}
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="mb-4">
                        <strong>Witness:</strong><br><br>
                        _________________________<br>
                        Name: ___________________<br>
                        Date: ___________________
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>