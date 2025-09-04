<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_data_id')->constrained('employee_data')->onDelete('cascade');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('hra_percentage', 5, 2)->default(40.00);
            $table->decimal('traveling_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(200);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
