<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_data_id')->constrained('employee_data')->onDelete('cascade');
            $table->foreignId('hr_id')->constrained('hrs')->onDelete('cascade');
            $table->date('offer_date');
            $table->date('joining_date');
            $table->decimal('offered_salary', 10, 2);
            $table->string('probation_period')->default('6 months');
            $table->text('job_description')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_letters');
    }
};