<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id'); // matches departments.id
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('passport_photo')->nullable();
            $table->text('current_address');
            $table->string('extra_mobile')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('aadhar_doc')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('pan_doc')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('driving_license_doc')->nullable();
            $table->string('voter_id')->nullable();
            $table->string('voter_id_doc')->nullable();
            $table->string('ctc')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('passbook_image')->nullable();
            $table->string('resume')->nullable();
            $table->enum('experience_type', ['fresher', 'experience']);
            $table->string('prev_offer_letter')->nullable();
            $table->string('prev_appointment_letter')->nullable();
            $table->string('prev_salary_slips')->nullable();
            $table->string('prev_relieving_letter')->nullable();
            $table->string('form_16')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_data');
    }
};
