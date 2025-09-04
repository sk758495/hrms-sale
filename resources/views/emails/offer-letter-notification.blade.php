<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin-bottom: 20px; }
        .signature { margin-top: 30px; }
        .highlight { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Jashma InfoSoft Pvt. Ltd.</h2>
        </div>
        
        <div class="content">
            <p><strong>Dear {{ $offerLetter->user->name }},</strong></p>
            
            <p>With reference to your application and subsequent assessments you had with us, we are pleased to offer you the position of <span class="highlight">{{ $offerLetter->employeeData->position->title ?? 'Employee' }}</span> at <span class="highlight">Jashma InfoSoft Pvt. Ltd.</span> Your Date of joining will be <span class="highlight">{{ $offerLetter->joining_date->format('d F Y') }}</span> at <span class="highlight">10:00 AM</span>.</p>
            
            <p>Your Cost to the Company (CTC) would be as discussed over the call. The detailed salary break up will be included in your 'Appointment Letter'.</p>
            
            <p><strong>You are requested to share the confirmation of your joining by replying to this email within the next 2 days.</strong></p>
            
            <p><em>In case of failure of acceptance, this offer will be revoked & considered invalid.</em></p>
            
            <p>You will undergo a probationary period for the initial three months from your date of joining. The extension of this period will be based on your performance and adherence to our established work ethics. Your appointment will be confirmed subsequent to the reception of the appointment letter only.</p>
            
            <p>We're excited about the possibility of you joining our team and look forward to your response.</p>
        </div>
        
        <div class="signature">
            <p><strong>Best regards,</strong><br>
            {{ $offerLetter->hr->name }}</p>
        </div>
    </div>
</body>
</html>