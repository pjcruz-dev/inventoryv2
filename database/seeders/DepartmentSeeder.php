<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Information Technology', 'description' => 'IT department responsible for technology infrastructure'],
            ['name' => 'Human Resources', 'description' => 'HR department managing employee relations'],
            ['name' => 'Finance', 'description' => 'Finance department handling financial operations'],
            ['name' => 'Operations', 'description' => 'Operations department managing daily business activities'],
            ['name' => 'Marketing', 'description' => 'Marketing department handling promotional activities'],
            ['name' => 'Sales', 'description' => 'Sales department managing customer relationships'],
            ['name' => 'Administration', 'description' => 'Administrative department handling general office management']
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
