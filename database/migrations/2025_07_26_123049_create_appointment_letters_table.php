<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_data_id')->constrained('employee_data')->onDelete('cascade');
            $table->foreignId('hr_id')->constrained('hrs')->onDelete('cascade');
            $table->date('appointment_date');
            $table->date('joining_date');
            $table->text('terms_conditions')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_letters');
    }
};