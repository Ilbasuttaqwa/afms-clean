<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceRule;

class AttendanceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Standard Office Hours',
                'check_in_time' => '08:00:00',
                'check_out_time' => '17:00:00',
                'late_threshold' => '08:15:00',
                'early_leave_threshold' => '16:45:00',
                'work_hours' => 8,
                'break_time' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Flexible Hours',
                'check_in_time' => '09:00:00',
                'check_out_time' => '18:00:00',
                'late_threshold' => '09:15:00',
                'early_leave_threshold' => '17:45:00',
                'work_hours' => 8,
                'break_time' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Shift Morning',
                'check_in_time' => '06:00:00',
                'check_out_time' => '14:00:00',
                'late_threshold' => '06:15:00',
                'early_leave_threshold' => '13:45:00',
                'work_hours' => 8,
                'break_time' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            AttendanceRule::create($rule);
        }
    }
}
