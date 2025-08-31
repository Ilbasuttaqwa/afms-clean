<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Position;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'John Doe',
                'employee_id' => 'EMP001',
                'email' => 'john.doe@afms.com',
                'phone' => '+6281234567890',
                'branch_id' => 1,
                'position_id' => 2,
                'department' => 'IT',
                'hire_date' => '2024-01-15',
                'status' => 'active',
            ],
            [
                'name' => 'Jane Smith',
                'employee_id' => 'EMP002',
                'email' => 'jane.smith@afms.com',
                'phone' => '+6281234567891',
                'branch_id' => 1,
                'position_id' => 3,
                'department' => 'HR',
                'hire_date' => '2024-02-01',
                'status' => 'active',
            ],
            [
                'name' => 'Bob Johnson',
                'employee_id' => 'EMP003',
                'email' => 'bob.johnson@afms.com',
                'phone' => '+6281234567892',
                'branch_id' => 2,
                'position_id' => 4,
                'department' => 'Finance',
                'hire_date' => '2024-03-01',
                'status' => 'active',
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
