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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->string('interviewer_name');
            $table->string('contact_number');
            $table->string('email')->nullable();
            $table->string('resume')->nullable();
            $table->enum('employee_type', ['Fresher', 'Experienced']);
            $table->decimal('current_salary', 10, 2)->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->string('status')->default('Pending');
            $table->text('remark_1')->nullable();
            $table->timestamp('remark_1_created_at')->nullable();
            $table->text('remark_2')->nullable();
            $table->timestamp('remark_2_created_at')->nullable();
            $table->text('remark_3')->nullable();
            $table->timestamp('remark_3_created_at')->nullable();
            $table->dateTime('interview_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
