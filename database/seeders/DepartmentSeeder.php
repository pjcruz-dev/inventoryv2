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
        // Check if departments already exist to prevent duplicates
        if (Department::count() > 0) {
            $this->command->info('Departments already exist. Skipping seeder to prevent duplicates.');
            return;
        }

        // Create parent divisions first
        $parentDivisions = [
            ['name' => 'Roll-Out', 'description' => 'Division responsible for project rollout and implementation'],
            ['name' => 'Operations & Maintenance', 'description' => 'Division managing operational activities and maintenance'],
            ['name' => 'WHSE', 'description' => ''],
            ['name' => 'Security', 'description' => 'Security division ensuring safety and protection'],
            ['name' => 'Commercial', 'description' => 'Commercial division managing business operations'],
            ['name' => 'External Affairs', 'description' => 'Division managing external relationships and communications'],
            ['name' => 'Human Resources and Administration', 'description' => 'HR and administrative division managing personnel and office operations'],
            ['name' => 'Finance', 'description' => 'Finance division handling financial operations'],
            ['name' => 'Legal & Documentation', 'description' => 'Legal division handling documentation and compliance']
        ];

        $createdParents = [];
        foreach ($parentDivisions as $division) {
            $parent = Department::create($division);
            $createdParents[$parent->name] = $parent->id;
            $this->command->info("Created parent department: {$parent->name}");
        }

        // Create sub-departments with dynamic parent IDs
        $subDepartments = [
            // Roll-Out sub-departments
            ['name' => 'Site Acquisition', 'description' => 'Department handling site acquisition activities', 'parent_name' => 'Roll-Out'],
            ['name' => 'CVE and Structural Design', 'description' => 'Civil and structural design department', 'parent_name' => 'Roll-Out'],
            ['name' => 'CVE Implementation', 'description' => 'Civil engineering implementation department', 'parent_name' => 'Roll-Out'],
            ['name' => 'Quality Assurance', 'description' => 'Quality assurance department', 'parent_name' => 'Roll-Out'],
            ['name' => 'Infrastructure Services Delivery', 'description' => 'Infrastructure services delivery department', 'parent_name' => 'Roll-Out'],
            
            // Operations & Maintenance sub-departments
            ['name' => 'Field Operations', 'description' => 'Field operations department', 'parent_name' => 'Operations & Maintenance'],
            ['name' => 'Energy Management', 'description' => 'Energy management department', 'parent_name' => 'Operations & Maintenance'],
            ['name' => 'Performance Analytics', 'description' => 'Performance analytics department', 'parent_name' => 'Operations & Maintenance'],
            ['name' => 'Operations Excellence', 'description' => 'Operations excellence department', 'parent_name' => 'Operations & Maintenance'],
            ['name' => 'Information and Communications Technology', 'description' => 'ICT department', 'parent_name' => 'Operations & Maintenance'],
            
            // WHSE sub-departments
            ['name' => 'WHSE Roll-Out', 'description' => 'Warehouse rollout department', 'parent_name' => 'WHSE'],
            ['name' => 'Environment, Social and Governance', 'description' => 'ESG department', 'parent_name' => 'WHSE'],
            
            // Security sub-departments
            ['name' => 'Security Operations', 'description' => 'Security operations department', 'parent_name' => 'Security'],
            ['name' => 'Regional Security (NCR & Luzon)', 'description' => 'Regional security for NCR and Luzon', 'parent_name' => 'Security'],
            ['name' => 'Regional Security (Visayas)', 'description' => 'Regional security for Visayas', 'parent_name' => 'Security'],
            ['name' => 'Regional Security (Mindanao)', 'description' => 'Regional security for Mindanao', 'parent_name' => 'Security'],
            
            // Commercial sub-departments
            ['name' => 'Account Management', 'description' => 'Account management department', 'parent_name' => 'Commercial'],
            ['name' => 'Techno-Commercial', 'description' => 'Techno-commercial department', 'parent_name' => 'Commercial'],
            
            // External Affairs sub-departments
            ['name' => 'Regulatory, Public Policy & Industry Affairs', 'description' => 'Regulatory and policy affairs department', 'parent_name' => 'External Affairs'],
            ['name' => 'Community, Political, and Industry Stakeholder Affairs', 'description' => 'Community and stakeholder affairs department', 'parent_name' => 'External Affairs'],
            ['name' => 'Government Relations', 'description' => 'Government relations department', 'parent_name' => 'External Affairs'],
            ['name' => 'Corporate Communications, Public Relations and Media Affairs', 'description' => 'Corporate communications department', 'parent_name' => 'External Affairs'],
            
            // HR and Administration sub-departments
            ['name' => 'HR Operations', 'description' => 'HR operations department', 'parent_name' => 'Human Resources and Administration'],
            ['name' => 'Talent & Organization Management', 'description' => 'Talent and organization management department', 'parent_name' => 'Human Resources and Administration'],
            ['name' => 'Administration', 'description' => 'Administration department', 'parent_name' => 'Human Resources and Administration'],
            
            // Finance sub-departments
            ['name' => 'Corporate Finance & Risk', 'description' => 'Corporate finance and risk department', 'parent_name' => 'Finance'],
            ['name' => 'Financial Reporting', 'description' => 'Financial reporting department', 'parent_name' => 'Finance'],
            ['name' => 'Lessor Asset Management', 'description' => 'Lessor asset management department', 'parent_name' => 'Finance'],
            ['name' => 'Supply Chain Management', 'description' => 'Supply chain management department', 'parent_name' => 'Finance'],
            
            // Legal & Documentation sub-departments
            ['name' => 'Legal (PTCI)', 'description' => 'Legal department for PTCI', 'parent_name' => 'Legal & Documentation'],
            ['name' => 'Legal (MIDC)', 'description' => 'Legal department for MIDC', 'parent_name' => 'Legal & Documentation'],
            ['name' => 'Documentation Control', 'description' => 'Documentation control department', 'parent_name' => 'Legal & Documentation']
        ];

        foreach ($subDepartments as $department) {
            $parentName = $department['parent_name'];
            unset($department['parent_name']);
            $department['parent_id'] = $createdParents[$parentName];
            $subDept = Department::create($department);
            $this->command->info("Created sub-department: {$subDept->name} under {$parentName}");
        }

        $this->command->info('Department seeding completed successfully!');
        $this->command->info('Total parent departments: ' . count($createdParents));
        $this->command->info('Total sub-departments: ' . count($subDepartments));
        $this->command->info('Total departments created: ' . Department::count());
    }
}
