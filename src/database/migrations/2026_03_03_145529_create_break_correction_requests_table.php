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
        Schema::create('break_correction_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('correction_request_id')
                ->constrained('attendance_correction_requests')
                ->cascadeOnDelete();

            $table->foreignId('break_id')
                ->nullable()
                ->constrained('breaks')
                ->nullOnDelete();

            $table->dateTime('requested_break_start');
            $table->dateTime('requested_break_end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('break_correction_requests');
    }
};
