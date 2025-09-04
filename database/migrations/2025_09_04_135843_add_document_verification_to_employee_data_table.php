<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_data', function (Blueprint $table) {
            $table->enum('passport_photo_status', ['pending', 'approved', 'rejected'])->default('pending')->after('passport_photo');
            $table->text('passport_photo_remarks')->nullable()->after('passport_photo_status');
            
            $table->enum('aadhar_doc_status', ['pending', 'approved', 'rejected'])->default('pending')->after('aadhar_doc');
            $table->text('aadhar_doc_remarks')->nullable()->after('aadhar_doc_status');
            
            $table->enum('pan_doc_status', ['pending', 'approved', 'rejected'])->default('pending')->after('pan_doc');
            $table->text('pan_doc_remarks')->nullable()->after('pan_doc_status');
            
            $table->enum('driving_license_doc_status', ['pending', 'approved', 'rejected'])->default('pending')->after('driving_license_doc');
            $table->text('driving_license_doc_remarks')->nullable()->after('driving_license_doc_status');
            
            $table->enum('voter_id_doc_status', ['pending', 'approved', 'rejected'])->default('pending')->after('voter_id_doc');
            $table->text('voter_id_doc_remarks')->nullable()->after('voter_id_doc_status');
            
            $table->enum('passbook_image_status', ['pending', 'approved', 'rejected'])->default('pending')->after('passbook_image');
            $table->text('passbook_image_remarks')->nullable()->after('passbook_image_status');
            
            $table->enum('resume_status', ['pending', 'approved', 'rejected'])->default('pending')->after('resume');
            $table->text('resume_remarks')->nullable()->after('resume_status');
            
            $table->enum('prev_offer_letter_status', ['pending', 'approved', 'rejected'])->default('pending')->after('prev_offer_letter');
            $table->text('prev_offer_letter_remarks')->nullable()->after('prev_offer_letter_status');
            
            $table->enum('prev_appointment_letter_status', ['pending', 'approved', 'rejected'])->default('pending')->after('prev_appointment_letter');
            $table->text('prev_appointment_letter_remarks')->nullable()->after('prev_appointment_letter_status');
            
            $table->enum('prev_salary_slips_status', ['pending', 'approved', 'rejected'])->default('pending')->after('prev_salary_slips');
            $table->text('prev_salary_slips_remarks')->nullable()->after('prev_salary_slips_status');
            
            $table->enum('prev_relieving_letter_status', ['pending', 'approved', 'rejected'])->default('pending')->after('prev_relieving_letter');
            $table->text('prev_relieving_letter_remarks')->nullable()->after('prev_relieving_letter_status');
            
            $table->enum('form_16_status', ['pending', 'approved', 'rejected'])->default('pending')->after('form_16');
            $table->text('form_16_remarks')->nullable()->after('form_16_status');
            
            $table->enum('overall_status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('employee_data', function (Blueprint $table) {
            $table->dropColumn([
                'passport_photo_status', 'passport_photo_remarks',
                'aadhar_doc_status', 'aadhar_doc_remarks',
                'pan_doc_status', 'pan_doc_remarks',
                'driving_license_doc_status', 'driving_license_doc_remarks',
                'voter_id_doc_status', 'voter_id_doc_remarks',
                'passbook_image_status', 'passbook_image_remarks',
                'resume_status', 'resume_remarks',
                'prev_offer_letter_status', 'prev_offer_letter_remarks',
                'prev_appointment_letter_status', 'prev_appointment_letter_remarks',
                'prev_salary_slips_status', 'prev_salary_slips_remarks',
                'prev_relieving_letter_status', 'prev_relieving_letter_remarks',
                'form_16_status', 'form_16_remarks',
                'overall_status'
            ]);
        });
    }
};