<?php

namespace Database\Factories;

use App\Enums\CorrectionStatus;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'requested_clock_in' => null,
            'requested_clock_out' => null,
            'requested_note' => fake()->text(50),
            'status' => CorrectionStatus::Pending,
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
}
