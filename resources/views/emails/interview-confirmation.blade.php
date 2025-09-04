{{-- Subject: Selection for {{ $interview->position->name }} Role at Jashma InfoSoft Pvt. Ltd. --}}

Dear {{ $interview->interviewer_name }},

With reference to your application and subsequent assessments you had with us, we are informing you that you are selected for the position of {{ $interview->position->name }} at Jashma InfoSoft Pvt. Ltd.

You are requested to share the softcopy of below listed documents within 2 days of receiving this email.

Documents Required at the time of Joining:

@if($interview->employee_type == 'Fresher')
For Freshers: 
1. Educational Degree : Photocopy of SSC, HSC, Bachelor's and Master's Degree (whichever applicable)
2. ID Proof : Photocopy of Aadhar Card, Pan Card, Driving license and Voter ID Card Copy
3. Passport Size Photos - Two
@else
For Experienced employees:
1. Educational Degree : Photocopy of SSC, HSC, Bachelor's and Master's Degree (whichever applicable)
2. ID Proof : Photocopy of Aadhar Card, Pan Card, Driving license and Voter ID Card Copy
3. Passport Size Photos - Two
4. Previous Companies Offer Letters, Appointment &  Relieving Letters
5. Recent 3 payslips or 3 Months' Bank Statements
6. Form 16 from previous employer
@endif

The offer letter email confirmation will be shared only after we receive the above requested documents.

Best regards,
{{ $hrName }}