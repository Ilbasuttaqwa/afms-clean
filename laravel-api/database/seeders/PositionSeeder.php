<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Administrator',
                'description' => 'System Administrator',
                'level' => 1,
            ],
            [
                'name' => 'Manager',
                'description' => 'Department Manager',
                'level' => 2,
            ],
            [
                'name' => 'Supervisor',
                'description' => 'Team Supervisor',
                'level' => 3,
            ],
            [
                'name' => 'Staff',
                'description' => 'Regular Staff',
                'level' => 4,
            ],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
