<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Computer;
use App\Models\Monitor;
use App\Models\Printer;
use App\Models\Peripheral;
use App\Models\Maintenance;
use App\Models\Disposal;
use App\Models\AssetAssignmentConfirmation;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive data seeding...');

        // Create vendors
        $this->createVendors();
        
        // Create additional users
        $this->createUsers();
        
        // Create assets
        $this->createAssets();
        
        // Create maintenance records
        $this->createMaintenanceRecords();
        
        // Create disposal records
        $this->createDisposalRecords();
        
        // Create asset assignment confirmations
        $this->createAssetAssignmentConfirmations();

        $this->command->info('✅ Comprehensive data seeding completed!');
    }

    private function createVendors()
    {
        $this->command->info('📦 Creating vendors...');
        
        $vendors = [
            [
                'name' => 'Dell Technologies',
                'contact_person' => 'John Smith',
                'email' => 'sales@dell.com',
                'phone' => '+1-800-999-3355',
                'address' => 'One Dell Way, Round Rock, TX 78682, USA'
            ],
            [
                'name' => 'HP Inc.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'enterprise@hp.com',
                'phone' => '+1-800-474-6836',
                'address' => '1501 Page Mill Road, Palo Alto, CA 94304, USA'
            ],
            [
                'name' => 'Lenovo',
                'contact_person' => 'Michael Chen',
                'email' => 'business@lenovo.com',
                'phone' => '+1-866-968-4466',
                'address' => '1009 Think Place, Morrisville, NC 27560, USA'
            ],
            [
                'name' => 'Apple Inc.',
                'contact_person' => 'Lisa Wang',
                'email' => 'business@apple.com',
                'phone' => '+1-800-854-3680',
                'address' => 'One Apple Park Way, Cupertino, CA 95014, USA'
            ],
            [
                'name' => 'Samsung Electronics',
                'contact_person' => 'David Kim',
                'email' => 'business@samsung.com',
                'phone' => '+1-800-726-7864',
                'address' => '85 Challenger Road, Ridgefield Park, NJ 07660, USA'
            ],
            [
                'name' => 'Canon Philippines',
                'contact_person' => 'Maria Santos',
                'email' => 'sales@canon.com.ph',
                'phone' => '+63-2-8888-8888',
                'address' => 'Canon Marketing Philippines, Inc., Makati City, Philippines'
            ],
            [
                'name' => 'Epson Philippines',
                'contact_person' => 'Roberto Cruz',
                'email' => 'business@epson.com.ph',
                'phone' => '+63-2-8888-7777',
                'address' => 'Epson Philippines Corporation, Quezon City, Philippines'
            ],
            [
                'name' => 'Cisco Systems',
                'contact_person' => 'Jennifer Lee',
                'email' => 'partners@cisco.com',
                'phone' => '+1-800-553-6387',
                'address' => '170 West Tasman Drive, San Jose, CA 95134, USA'
            ],
            [
                'name' => 'Microsoft Corporation',
                'contact_person' => 'Alex Rodriguez',
                'email' => 'licensing@microsoft.com',
                'phone' => '+1-800-642-7676',
                'address' => 'One Microsoft Way, Redmond, WA 98052, USA'
            ],
            [
                'name' => 'Logitech',
                'contact_person' => 'Emma Wilson',
                'email' => 'business@logitech.com',
                'phone' => '+1-646-454-3200',
                'address' => '7700 Gateway Boulevard, Newark, CA 94560, USA'
            ]
        ];

        foreach ($vendors as $vendorData) {
            Vendor::firstOrCreate(
                ['name' => $vendorData['name']],
                $vendorData
            );
        }

        $this->command->info('✅ Created ' . count($vendors) . ' vendors');
    }

    private function createUsers()
    {
        $this->command->info('👥 Creating additional users...');
        
        $departments = Department::all();
        $roles = \Spatie\Permission\Models\Role::all();
        $roleMap = $roles->keyBy('name');
        
        $users = [
            [
                'employee_id' => 'EMP001',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@company.com',
                'department_id' => $departments->where('name', 'HR Operations')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'HR Manager',
                'phone' => '+63-917-123-4567',
                'role_id' => $roleMap->get('Admin')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP002',
                'first_name' => 'Juan',
                'last_name' => 'Cruz',
                'email' => 'juan.cruz@company.com',
                'department_id' => $departments->where('name', 'Information and Communications Technology')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'IT Manager',
                'phone' => '+63-917-234-5678',
                'role_id' => $roleMap->get('Admin')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP003',
                'first_name' => 'Ana',
                'last_name' => 'Reyes',
                'email' => 'ana.reyes@company.com',
                'department_id' => $departments->where('name', 'Finance')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Finance Manager',
                'phone' => '+63-917-345-6789',
                'role_id' => $roleMap->get('Manager')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP004',
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'email' => 'carlos.mendoza@company.com',
                'department_id' => $departments->where('name', 'Field Operations')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Operations Supervisor',
                'phone' => '+63-917-456-7890',
                'role_id' => $roleMap->get('Manager')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP005',
                'first_name' => 'Lisa',
                'last_name' => 'Garcia',
                'email' => 'lisa.garcia@company.com',
                'department_id' => $departments->where('name', 'Security Operations')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Security Officer',
                'phone' => '+63-917-567-8901',
                'role_id' => $roleMap->get('User')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP006',
                'first_name' => 'Miguel',
                'last_name' => 'Torres',
                'email' => 'miguel.torres@company.com',
                'department_id' => $departments->where('name', 'Quality Assurance')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'QA Specialist',
                'phone' => '+63-917-678-9012',
                'role_id' => $roleMap->get('User')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP007',
                'first_name' => 'Sofia',
                'last_name' => 'Lopez',
                'email' => 'sofia.lopez@company.com',
                'department_id' => $departments->where('name', 'Account Management')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Account Manager',
                'phone' => '+63-917-789-0123',
                'role_id' => $roleMap->get('Manager')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP008',
                'first_name' => 'Roberto',
                'last_name' => 'Martinez',
                'email' => 'roberto.martinez@company.com',
                'department_id' => $departments->where('name', 'Energy Management')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Energy Analyst',
                'phone' => '+63-917-890-1234',
                'role_id' => $roleMap->get('User')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP009',
                'first_name' => 'Carmen',
                'last_name' => 'Vargas',
                'email' => 'carmen.vargas@company.com',
                'department_id' => $departments->where('name', 'Legal (PTCI)')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Legal Counsel',
                'phone' => '+63-917-901-2345',
                'role_id' => $roleMap->get('Manager')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ],
            [
                'employee_id' => 'EMP010',
                'first_name' => 'Diego',
                'last_name' => 'Hernandez',
                'email' => 'diego.hernandez@company.com',
                'department_id' => $departments->where('name', 'Performance Analytics')->first()->id ?? $departments->random()->id,
                'company' => 'MIDC',
                'position' => 'Data Analyst',
                'phone' => '+63-917-012-3456',
                'role_id' => $roleMap->get('User')->id ?? $roles->first()->id,
                'status' => 1,
                'password' => Hash::make('password123')
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Created ' . count($users) . ' additional users');
    }

    private function createAssets()
    {
        $this->command->info('💻 Creating assets...');
        
        $categories = AssetCategory::all();
        $vendors = Vendor::all();
        $users = User::all();
        $departments = Department::all();
        
        $assetData = [
            // Computers
            [
                'asset_tag' => 'COMP-001',
                'name' => 'Dell OptiPlex 7090 Desktop',
                'description' => 'High-performance desktop computer for office use',
                'serial_number' => 'DL7090-001',
                'model' => 'OptiPlex 7090',
                'category_id' => $categories->where('name', 'Computer Hardware')->first()->id,
                'vendor_id' => $vendors->where('name', 'Dell Technologies')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(6),
                'warranty_end' => Carbon::now()->addMonths(18),
                'cost' => 45000.00,
                'po_number' => 'PO-2024-001',
                'entity' => 'MIDC',
                'lifespan' => 5,
                'location' => 'Office Floor 2',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(5),
                'department_id' => $departments->random()->id
            ],
            [
                'asset_tag' => 'COMP-002',
                'name' => 'HP EliteBook 850 G8 Laptop',
                'description' => 'Business laptop with high security features',
                'serial_number' => 'HP850G8-002',
                'model' => 'EliteBook 850 G8',
                'category_id' => $categories->where('name', 'Computer Hardware')->first()->id,
                'vendor_id' => $vendors->where('name', 'HP Inc.')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(4),
                'warranty_end' => Carbon::now()->addMonths(20),
                'cost' => 65000.00,
                'po_number' => 'PO-2024-002',
                'entity' => 'MIDC',
                'lifespan' => 4,
                'location' => 'Office Floor 3',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(3),
                'department_id' => $departments->random()->id
            ],
            [
                'asset_tag' => 'COMP-003',
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'description' => 'Ultra-lightweight business laptop',
                'serial_number' => 'LX1C-003',
                'model' => 'ThinkPad X1 Carbon Gen 9',
                'category_id' => $categories->where('name', 'Computer Hardware')->first()->id,
                'vendor_id' => $vendors->where('name', 'Lenovo')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(2),
                'warranty_end' => Carbon::now()->addMonths(22),
                'cost' => 75000.00,
                'po_number' => 'PO-2024-003',
                'entity' => 'MIDC',
                'lifespan' => 4,
                'location' => 'Office Floor 1',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(1),
                'department_id' => $departments->random()->id
            ],
            // Monitors
            [
                'asset_tag' => 'MON-001',
                'name' => 'Dell UltraSharp 27" Monitor',
                'description' => '4K UHD monitor for professional use',
                'serial_number' => 'DL27U-001',
                'model' => 'U2720Q',
                'category_id' => $categories->where('name', 'Monitors')->first()->id,
                'vendor_id' => $vendors->where('name', 'Dell Technologies')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(8),
                'warranty_end' => Carbon::now()->addMonths(16),
                'cost' => 25000.00,
                'po_number' => 'PO-2024-004',
                'entity' => 'MIDC',
                'lifespan' => 6,
                'location' => 'Office Floor 2',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(7),
                'department_id' => $departments->random()->id
            ],
            [
                'asset_tag' => 'MON-002',
                'name' => 'Samsung 24" Business Monitor',
                'description' => 'Full HD business monitor',
                'serial_number' => 'SM24B-002',
                'model' => 'S24F350FH',
                'category_id' => $categories->where('name', 'Monitors')->first()->id,
                'vendor_id' => $vendors->where('name', 'Samsung Electronics')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(5),
                'warranty_end' => Carbon::now()->addMonths(19),
                'cost' => 12000.00,
                'po_number' => 'PO-2024-005',
                'entity' => 'MIDC',
                'lifespan' => 5,
                'location' => 'Office Floor 3',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(4),
                'department_id' => $departments->random()->id
            ],
            // Printers
            [
                'asset_tag' => 'PRN-001',
                'name' => 'Canon imageRUNNER 2625i',
                'description' => 'Multifunction printer for office use',
                'serial_number' => 'CN2625-001',
                'model' => 'imageRUNNER 2625i',
                'category_id' => $categories->where('name', 'Printers')->first()->id,
                'vendor_id' => $vendors->where('name', 'Canon Philippines')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(10),
                'warranty_end' => Carbon::now()->addMonths(14),
                'cost' => 85000.00,
                'po_number' => 'PO-2024-006',
                'entity' => 'MIDC',
                'lifespan' => 7,
                'location' => 'Office Floor 1',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(9),
                'department_id' => $departments->random()->id
            ],
            [
                'asset_tag' => 'PRN-002',
                'name' => 'Epson WorkForce Pro WF-3720',
                'description' => 'All-in-one wireless printer',
                'serial_number' => 'EP3720-002',
                'model' => 'WorkForce Pro WF-3720',
                'category_id' => $categories->where('name', 'Printers')->first()->id,
                'vendor_id' => $vendors->where('name', 'Epson Philippines')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(3),
                'warranty_end' => Carbon::now()->addMonths(21),
                'cost' => 15000.00,
                'po_number' => 'PO-2024-007',
                'entity' => 'MIDC',
                'lifespan' => 5,
                'location' => 'Office Floor 2',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(2),
                'department_id' => $departments->random()->id
            ],
            // Peripherals
            [
                'asset_tag' => 'PER-001',
                'name' => 'Logitech MX Master 3 Mouse',
                'description' => 'Wireless ergonomic mouse',
                'serial_number' => 'LGMM3-001',
                'model' => 'MX Master 3',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'vendor_id' => $vendors->where('name', 'Logitech')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(1),
                'warranty_end' => Carbon::now()->addMonths(23),
                'cost' => 3500.00,
                'po_number' => 'PO-2024-008',
                'entity' => 'MIDC',
                'lifespan' => 3,
                'location' => 'Office Floor 1',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subDays(15),
                'department_id' => $departments->random()->id
            ],
            [
                'asset_tag' => 'PER-002',
                'name' => 'Logitech K380 Wireless Keyboard',
                'description' => 'Compact wireless keyboard',
                'serial_number' => 'LGK380-002',
                'model' => 'K380',
                'category_id' => $categories->where('name', 'Peripherals')->first()->id,
                'vendor_id' => $vendors->where('name', 'Logitech')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(2),
                'warranty_end' => Carbon::now()->addMonths(22),
                'cost' => 2500.00,
                'po_number' => 'PO-2024-009',
                'entity' => 'MIDC',
                'lifespan' => 3,
                'location' => 'Office Floor 3',
                'status' => 'Active',
                'movement' => 'Assigned',
                'assigned_to' => $users->random()->id,
                'assigned_date' => Carbon::now()->subMonths(1),
                'department_id' => $departments->random()->id
            ],
            // Network Equipment
            [
                'asset_tag' => 'NET-001',
                'name' => 'Cisco Catalyst 2960 Switch',
                'description' => '24-port managed switch',
                'serial_number' => 'CS2960-001',
                'model' => 'Catalyst 2960-24TC-L',
                'category_id' => $categories->where('name', 'Network Equipment')->first()->id,
                'vendor_id' => $vendors->where('name', 'Cisco Systems')->first()->id,
                'purchase_date' => Carbon::now()->subMonths(12),
                'warranty_end' => Carbon::now()->addMonths(12),
                'cost' => 45000.00,
                'po_number' => 'PO-2024-010',
                'entity' => 'MIDC',
                'lifespan' => 8,
                'location' => 'Server Room',
                'status' => 'Active',
                'movement' => 'In Use',
                'assigned_to' => null,
                'assigned_date' => null,
                'department_id' => $departments->where('name', 'Information and Communications Technology')->first()->id
            ]
        ];

        foreach ($assetData as $asset) {
            Asset::firstOrCreate(
                ['asset_tag' => $asset['asset_tag']],
                $asset
            );
        }

        // Create specific asset type records
        $this->createComputerRecords();
        $this->createMonitorRecords();
        $this->createPrinterRecords();
        $this->createPeripheralRecords();

        $this->command->info('✅ Created ' . count($assetData) . ' assets');
    }

    private function createComputerRecords()
    {
        $computers = Asset::whereIn('asset_tag', ['COMP-001', 'COMP-002', 'COMP-003'])->get();
        
        foreach ($computers as $asset) {
            Computer::firstOrCreate(
                ['asset_id' => $asset->id],
                [
                    'processor' => 'Intel Core i7-11700',
                    'memory' => '16GB DDR4',
                    'storage' => '512GB SSD',
                    'graphics_card' => 'Intel UHD Graphics 750',
                    'operating_system' => 'Windows 11 Pro',
                    'computer_type' => 'Desktop'
                ]
            );
        }
    }

    private function createMonitorRecords()
    {
        $monitors = Asset::whereIn('asset_tag', ['MON-001', 'MON-002'])->get();
        
        foreach ($monitors as $asset) {
            Monitor::firstOrCreate(
                ['asset_id' => $asset->id],
                [
                    'size' => $asset->asset_tag === 'MON-001' ? '27"' : '24"',
                    'resolution' => $asset->asset_tag === 'MON-001' ? '3840x2160' : '1920x1080',
                    'panel_type' => 'IPS'
                ]
            );
        }
    }

    private function createPrinterRecords()
    {
        $printers = Asset::whereIn('asset_tag', ['PRN-001', 'PRN-002'])->get();
        
        foreach ($printers as $asset) {
            Printer::firstOrCreate(
                ['asset_id' => $asset->id],
                [
                    'type' => $asset->asset_tag === 'PRN-001' ? 'Multifunction' : 'All-in-One',
                    'color_support' => true,
                    'duplex' => true
                ]
            );
        }
    }

    private function createPeripheralRecords()
    {
        $peripherals = Asset::whereIn('asset_tag', ['PER-001', 'PER-002'])->get();
        
        foreach ($peripherals as $asset) {
            Peripheral::firstOrCreate(
                ['asset_id' => $asset->id],
                [
                    'type' => $asset->asset_tag === 'PER-001' ? 'Mouse' : 'Keyboard',
                    'interface' => 'Wireless'
                ]
            );
        }
    }

    private function createMaintenanceRecords()
    {
        $this->command->info('🔧 Creating maintenance records...');
        
        $assets = Asset::where('status', 'Active')->take(5)->get();
        
        foreach ($assets as $asset) {
            Maintenance::create([
                'asset_id' => $asset->id,
                'vendor_id' => Vendor::first()->id,
                'issue_reported' => 'Routine maintenance check',
                'repair_action' => 'System check and cleaning',
                'cost' => 5000.00,
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->subDays(25),
                'status' => 'Completed',
                'remarks' => 'All systems functioning properly'
            ]);
        }

        $this->command->info('✅ Created maintenance records');
    }

    private function createDisposalRecords()
    {
        $this->command->info('🗑️ Creating disposal records...');
        
        // Create some assets for disposal
        $disposalAssets = [
            [
                'asset_tag' => 'DISP-001',
                'name' => 'Old Dell Desktop',
                'description' => 'Retired desktop computer',
                'serial_number' => 'DL-OLD-001',
                'model' => 'OptiPlex 3010',
                'category_id' => AssetCategory::where('name', 'Computer Hardware')->first()->id,
                'vendor_id' => Vendor::where('name', 'Dell Technologies')->first()->id,
                'purchase_date' => Carbon::now()->subYears(6),
                'warranty_end' => Carbon::now()->subYears(1),
                'cost' => 25000.00,
                'po_number' => 'PO-2018-001',
                'entity' => 'MIDC',
                'lifespan' => 5,
                'location' => 'Storage Room',
                'status' => 'Disposed',
                'movement' => 'Disposed',
                'assigned_to' => null,
                'assigned_date' => null,
                'department_id' => Department::first()->id
            ]
        ];

        foreach ($disposalAssets as $assetData) {
            $asset = Asset::firstOrCreate(
                ['asset_tag' => $assetData['asset_tag']],
                $assetData
            );

            Disposal::create([
                'asset_id' => $asset->id,
                'disposal_type' => 'Recycling',
                'disposal_value' => 2000.00,
                'disposal_date' => Carbon::now()->subDays(10),
                'approved_by' => User::first()->id,
                'remarks' => 'Asset recycled through certified vendor - End of Life'
            ]);
        }

        $this->command->info('✅ Created disposal records');
    }

    private function createAssetAssignmentConfirmations()
    {
        $this->command->info('📋 Creating asset assignment confirmations...');
        
        $assignedAssets = Asset::where('status', 'Active')
            ->whereNotNull('assigned_to')
            ->take(3)
            ->get();

        foreach ($assignedAssets as $asset) {
            AssetAssignmentConfirmation::create([
                'asset_id' => $asset->id,
                'user_id' => $asset->assigned_to,
                'status' => 'pending',
                'assigned_at' => Carbon::now()->subDays(5),
                'confirmation_token' => \Str::random(32)
            ]);
        }

        $this->command->info('✅ Created asset assignment confirmations');
    }
}
