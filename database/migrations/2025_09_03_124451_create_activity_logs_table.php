<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('timestamp');
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};