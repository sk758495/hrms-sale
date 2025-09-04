<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_data_id')->constrained('employee_data')->onDelete('cascade');
            $table->foreignId('hr_id')->constrained('hrs')->onDelete('cascade');

            $table->string('month_year');
            $table->date('joining_date');
            $table->integer('present_days');
            $table->integer('leave_taken')->default(0);
            $table->integer('balance_leave')->default(0);
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('traveling_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('miscellaneous', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(0);
            $table->decimal('advance_pay', 10, 2)->default(0);
            $table->decimal('arrears_deductions', 10, 2)->default(0);
            $table->decimal('total_earnings', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};