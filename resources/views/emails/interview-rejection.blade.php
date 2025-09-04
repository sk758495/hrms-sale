@if($rejectionType == 'no_position')
{{-- Subject: Application Status Update at Jashma InfoSoft Pvt. Ltd. --}}

Dear {{ $interview->interviewer_name }}

Thank you for your time in applying with Jashma InfoSoft Pvt. Ltd.

Unfortunately, we do not have any open position relevant to your profile at the moment.

We genuinely appreciate your interest in our company and wish you success in your job search.

Please keep an eye on our future job openings, as your skills and experience may align with other opportunities in future.

Best regards,
{{ $hrName }}

@elseif($rejectionType == 'high_ctc')
{{-- Subject: High CTC expectation - Rejection email --}}

Dear {{ $interview->interviewer_name }}

Thank you for your interest in exploring opportunities with Jashma InfoSoft Pvt. Ltd.

We truly appreciate the time and effort you invested in the application process. After careful consideration, we regret to inform you that we will not be moving forward with your application at this time. The compensation expectations associated with your profile currently exceed the budgeted range for the position.

We value your achievements and experience and encourage you to keep an eye on our future openings, as there may be more suitable opportunities ahead that better align with your profile.

To learn more about us and stay updated, please visit www.jashmainfo.com

Wishing you the very best in your job search and future endeavors.

Best regards,
{{ $hrName }}

@elseif($rejectionType == 'after_interview')
{{-- Subject: Application Status Update at Jashma InfoSoft Pvt. Ltd. --}}

Dear {{ $interview->interviewer_name }}

Thank you for your time in our interview evaluation process.

After careful review, we have determined that your current experience and skill sets do not align with the specific requirements we are seeking for this role. We have decided to move forward with candidates whose backgrounds more closely match our hiring needs.

We genuinely appreciate your interest in our company and wish you success in your job search.

Please keep an eye on our future job openings, as your skills and experience may align with other opportunities in future.

Thank you again for considering our company, and we wish you the best in your career endeavors.

Visit www.jashmainfo.com to know more about us.

Best regards,
{{ $hrName }}
@endif