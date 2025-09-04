<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->datetime('check_in')->nullable();
            $table->datetime('check_out')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('status')->default('absent'); // present, absent, late
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};