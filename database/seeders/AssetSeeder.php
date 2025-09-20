<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\User;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Peripheral;
use App\Models\Monitor;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Asset Seeder...');

        try {
            // Get category IDs
            $computerCategory = AssetCategory::where('name', 'Computer Hardware')->first();
            $monitorCategory = AssetCategory::where('name', 'Monitors')->first();
            $printerCategory = AssetCategory::where('name', 'Printers')->first();
            $peripheralCategory = AssetCategory::where('name', 'Peripherals')->first();

            if (!$computerCategory || !$monitorCategory || !$printerCategory || !$peripheralCategory) {
                $this->command->error('Required asset categories not found. Creating default categories...');
                $this->createDefaultCategories();
                
                // Re-fetch categories
                $computerCategory = AssetCategory::where('name', 'Computer Hardware')->first();
                $monitorCategory = AssetCategory::where('name', 'Monitors')->first();
                $printerCategory = AssetCategory::where('name', 'Printers')->first();
                $peripheralCategory = AssetCategory::where('name', 'Peripherals')->first();
            }

        // Get vendor IDs or create default vendors
        $vendors = Vendor::all();
        if ($vendors->isEmpty()) {
            $this->command->info('No vendors found. Creating default vendors...');
            $vendors = $this->createDefaultVendors();
        }

        // Get departments or create default departments
        $departments = Department::all();
        if ($departments->isEmpty()) {
            $this->command->info('No departments found. Creating default departments...');
            $departments = $this->createDefaultDepartments();
        }

        // Get users for assignment
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Assets will be created without user assignments.');
        }

        // Get available columns from assets table
        $assetColumns = Schema::getColumnListing('assets');

        // Asset data with realistic brands and models
        $assetData = [
            // Computers (15 records)
            'computers' => [
                ['name' => 'Dell OptiPlex 7090', 'model' => 'OptiPlex 7090', 'brand' => 'Dell'],
                ['name' => 'HP EliteDesk 800 G8', 'model' => 'EliteDesk 800 G8', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkCentre M920', 'model' => 'ThinkCentre M920', 'brand' => 'Lenovo'],
                ['name' => 'Dell Precision 3650', 'model' => 'Precision 3650', 'brand' => 'Dell'],
                ['name' => 'HP ProDesk 400 G8', 'model' => 'ProDesk 400 G8', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkCentre M720', 'model' => 'ThinkCentre M720', 'brand' => 'Lenovo'],
                ['name' => 'Dell OptiPlex 3080', 'model' => 'OptiPlex 3080', 'brand' => 'Dell'],
                ['name' => 'HP EliteDesk 705 G8', 'model' => 'EliteDesk 705 G8', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkCentre M920q', 'model' => 'ThinkCentre M920q', 'brand' => 'Lenovo'],
                ['name' => 'Dell Precision 5820', 'model' => 'Precision 5820', 'brand' => 'Dell'],
                ['name' => 'HP Z2 Tower G8', 'model' => 'Z2 Tower G8', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkStation P330', 'model' => 'ThinkStation P330', 'brand' => 'Lenovo'],
                ['name' => 'Dell OptiPlex 7090 Ultra', 'model' => 'OptiPlex 7090 Ultra', 'brand' => 'Dell'],
                ['name' => 'HP EliteOne 800 G8', 'model' => 'EliteOne 800 G8', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkCentre M90n', 'model' => 'ThinkCentre M90n', 'brand' => 'Lenovo'],
            ],

            // Monitors (10 records)
            'monitors' => [
                ['name' => 'Dell UltraSharp U2720Q', 'model' => 'U2720Q', 'brand' => 'Dell'],
                ['name' => 'HP EliteDisplay E243d', 'model' => 'E243d', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkVision P24h', 'model' => 'P24h', 'brand' => 'Lenovo'],
                ['name' => 'Dell P2720DC', 'model' => 'P2720DC', 'brand' => 'Dell'],
                ['name' => 'HP EliteDisplay E273q', 'model' => 'E273q', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkVision T24h', 'model' => 'T24h', 'brand' => 'Lenovo'],
                ['name' => 'Dell UltraSharp U2419H', 'model' => 'U2419H', 'brand' => 'Dell'],
                ['name' => 'HP EliteDisplay E243m', 'model' => 'E243m', 'brand' => 'HP'],
                ['name' => 'Lenovo ThinkVision P27h', 'model' => 'P27h', 'brand' => 'Lenovo'],
                ['name' => 'Dell P2419H', 'model' => 'P2419H', 'brand' => 'Dell'],
            ],

            // Printers (10 records)
            'printers' => [
                ['name' => 'HP LaserJet Pro M404n', 'model' => 'LaserJet Pro M404n', 'brand' => 'HP'],
                ['name' => 'Canon imageCLASS LBP6030w', 'model' => 'imageCLASS LBP6030w', 'brand' => 'Canon'],
                ['name' => 'Brother HL-L2350DW', 'model' => 'HL-L2350DW', 'brand' => 'Brother'],
                ['name' => 'HP LaserJet Enterprise M507x', 'model' => 'LaserJet Enterprise M507x', 'brand' => 'HP'],
                ['name' => 'Canon imageCLASS MF445dw', 'model' => 'imageCLASS MF445dw', 'brand' => 'Canon'],
                ['name' => 'Brother MFC-L2750DW', 'model' => 'MFC-L2750DW', 'brand' => 'Brother'],
                ['name' => 'HP OfficeJet Pro 9015e', 'model' => 'OfficeJet Pro 9015e', 'brand' => 'HP'],
                ['name' => 'Canon PIXMA TR8620', 'model' => 'PIXMA TR8620', 'brand' => 'Canon'],
                ['name' => 'Brother DCP-L2550DW', 'model' => 'DCP-L2550DW', 'brand' => 'Brother'],
                ['name' => 'HP LaserJet Pro M15w', 'model' => 'LaserJet Pro M15w', 'brand' => 'HP'],
            ],

            // Peripherals (15 records)
            'peripherals' => [
                ['name' => 'Logitech MX Master 3', 'model' => 'MX Master 3', 'brand' => 'Logitech'],
                ['name' => 'Microsoft Surface Keyboard', 'model' => 'Surface Keyboard', 'brand' => 'Microsoft'],
                ['name' => 'Logitech C920 HD Pro Webcam', 'model' => 'C920 HD Pro', 'brand' => 'Logitech'],
                ['name' => 'Razer BlackWidow V3', 'model' => 'BlackWidow V3', 'brand' => 'Razer'],
                ['name' => 'Corsair K95 RGB Platinum', 'model' => 'K95 RGB Platinum', 'brand' => 'Corsair'],
                ['name' => 'SteelSeries Rival 600', 'model' => 'Rival 600', 'brand' => 'SteelSeries'],
                ['name' => 'Logitech Z623 2.1 Speaker System', 'model' => 'Z623', 'brand' => 'Logitech'],
                ['name' => 'Blue Yeti USB Microphone', 'model' => 'Yeti USB', 'brand' => 'Blue'],
                ['name' => 'Anker PowerExpand Elite 13-in-1', 'model' => 'PowerExpand Elite', 'brand' => 'Anker'],
                ['name' => 'SanDisk Extreme Pro USB 3.2', 'model' => 'Extreme Pro USB 3.2', 'brand' => 'SanDisk'],
                ['name' => 'Logitech MX Keys', 'model' => 'MX Keys', 'brand' => 'Logitech'],
                ['name' => 'Corsair Harpoon RGB Pro', 'model' => 'Harpoon RGB Pro', 'brand' => 'Corsair'],
                ['name' => 'HyperX Cloud II Gaming Headset', 'model' => 'Cloud II', 'brand' => 'HyperX'],
                ['name' => 'Belkin USB-C Hub', 'model' => 'USB-C Hub', 'brand' => 'Belkin'],
                ['name' => 'Logitech Brio 4K Webcam', 'model' => 'Brio 4K', 'brand' => 'Logitech'],
            ],
        ];

        $createdAssets = [];

        // Create Computers
        $this->command->info('Creating Computer assets...');
        foreach ($assetData['computers'] as $index => $computerData) {
            $asset = $this->createAsset($computerData, $computerCategory->id, $vendors, $departments, $users, $assetColumns);
            
            // Create Computer record
            Computer::create([
                'asset_id' => $asset->id,
                'processor' => $this->getRandomProcessor(),
                'memory' => $this->getRandomMemory(),
                'storage' => $this->getRandomStorage(),
                'operating_system' => $this->getRandomOS(),
                'graphics_card' => $this->getRandomGraphicsCard(),
                'computer_type' => $this->getRandomComputerType(),
            ]);
            
            $createdAssets[] = $asset;
        }

        // Create Monitors
        $this->command->info('Creating Monitor assets...');
        foreach ($assetData['monitors'] as $index => $monitorData) {
            $asset = $this->createAsset($monitorData, $monitorCategory->id, $vendors, $departments, $users, $assetColumns);
            
            // Create Monitor record
            Monitor::create([
                'asset_id' => $asset->id,
                'size' => $this->getRandomMonitorSize(),
                'resolution' => $this->getRandomResolution(),
                'panel_type' => $this->getRandomPanelType(),
            ]);
            
            $createdAssets[] = $asset;
        }

        // Create Printers
        $this->command->info('Creating Printer assets...');
        foreach ($assetData['printers'] as $index => $printerData) {
            $asset = $this->createAsset($printerData, $printerCategory->id, $vendors, $departments, $users, $assetColumns);
            
            // Create Printer record
            Printer::create([
                'asset_id' => $asset->id,
                'type' => $this->getRandomPrinterType(),
                'color_support' => rand(0, 1),
                'duplex' => rand(0, 1),
            ]);
            
            $createdAssets[] = $asset;
        }

        // Create Peripherals
        $this->command->info('Creating Peripheral assets...');
        foreach ($assetData['peripherals'] as $index => $peripheralData) {
            $asset = $this->createAsset($peripheralData, $peripheralCategory->id, $vendors, $departments, $users, $assetColumns);
            
            // Create Peripheral record
            Peripheral::create([
                'asset_id' => $asset->id,
                'type' => $this->getRandomPeripheralType($peripheralData['name']),
                'interface' => $this->getRandomInterface($peripheralData['name']),
            ]);
            
            $createdAssets[] = $asset;
        }

        // Create 50 standalone assets (not linked to specific device types)
        $this->command->info('Creating 50 standalone assets...');
        $standaloneAssets = [
            // Network Equipment
            ['name' => 'Cisco Catalyst 2960 Switch', 'model' => 'WS-C2960-24TC-L', 'brand' => 'Cisco', 'category' => 'Network Equipment'],
            ['name' => 'Netgear ProSAFE 24-Port Switch', 'model' => 'GS724T', 'brand' => 'Netgear', 'category' => 'Network Equipment'],
            ['name' => 'TP-Link Archer C7 Router', 'model' => 'AC1750', 'brand' => 'TP-Link', 'category' => 'Network Equipment'],
            ['name' => 'Ubiquiti UniFi AP AC Pro', 'model' => 'UAP-AC-PRO', 'brand' => 'Ubiquiti', 'category' => 'Network Equipment'],
            ['name' => 'Cisco RV320 Dual WAN VPN Router', 'model' => 'RV320', 'brand' => 'Cisco', 'category' => 'Network Equipment'],
            ['name' => 'Netgear ReadyNAS 314', 'model' => 'RN314', 'brand' => 'Netgear', 'category' => 'Network Equipment'],
            ['name' => 'D-Link DGS-1024D Switch', 'model' => 'DGS-1024D', 'brand' => 'D-Link', 'category' => 'Network Equipment'],
            ['name' => 'Linksys WRT3200ACM Router', 'model' => 'WRT3200ACM', 'brand' => 'Linksys', 'category' => 'Network Equipment'],

            // Mobile Devices
            ['name' => 'iPhone 13 Pro', 'model' => 'A2487', 'brand' => 'Apple', 'category' => 'Mobile Devices'],
            ['name' => 'Samsung Galaxy S21 Ultra', 'model' => 'SM-G998B', 'brand' => 'Samsung', 'category' => 'Mobile Devices'],
            ['name' => 'iPad Pro 12.9-inch', 'model' => 'A2377', 'brand' => 'Apple', 'category' => 'Mobile Devices'],
            ['name' => 'Samsung Galaxy Tab S7', 'model' => 'SM-T870', 'brand' => 'Samsung', 'category' => 'Mobile Devices'],
            ['name' => 'Google Pixel 6 Pro', 'model' => 'G8VOU', 'brand' => 'Google', 'category' => 'Mobile Devices'],
            ['name' => 'OnePlus 9 Pro', 'model' => 'LE2123', 'brand' => 'OnePlus', 'category' => 'Mobile Devices'],
            ['name' => 'Huawei P40 Pro', 'model' => 'ELS-NX9', 'brand' => 'Huawei', 'category' => 'Mobile Devices'],
            ['name' => 'Xiaomi Mi 11 Ultra', 'model' => 'M2102K1G', 'brand' => 'Xiaomi', 'category' => 'Mobile Devices'],

            // Office Equipment
            ['name' => 'Epson PowerLite 1781W Projector', 'model' => 'PowerLite 1781W', 'brand' => 'Epson', 'category' => 'Office Equipment'],
            ['name' => 'BenQ MX550 Business Projector', 'model' => 'MX550', 'brand' => 'BenQ', 'category' => 'Office Equipment'],
            ['name' => 'Samsung Flip 2 Digital Whiteboard', 'model' => 'WM55H', 'brand' => 'Samsung', 'category' => 'Office Equipment'],
            ['name' => 'Logitech Rally Bar Video Conferencing', 'model' => 'Rally Bar', 'brand' => 'Logitech', 'category' => 'Office Equipment'],
            ['name' => 'Poly Studio X50 Video Bar', 'model' => 'Studio X50', 'brand' => 'Poly', 'category' => 'Office Equipment'],
            ['name' => 'Cisco Webex Board Pro', 'model' => 'G2-75', 'brand' => 'Cisco', 'category' => 'Office Equipment'],
            ['name' => 'Microsoft Surface Hub 2S', 'model' => 'SH2-85', 'brand' => 'Microsoft', 'category' => 'Office Equipment'],
            ['name' => 'ViewSonic IFP7550 Interactive Display', 'model' => 'IFP7550', 'brand' => 'ViewSonic', 'category' => 'Office Equipment'],

            // Storage Devices
            ['name' => 'WD My Cloud EX2 Ultra NAS', 'model' => 'WDBVBZ0080JCH', 'brand' => 'Western Digital', 'category' => 'Storage Devices'],
            ['name' => 'Synology DiskStation DS220+', 'model' => 'DS220+', 'brand' => 'Synology', 'category' => 'Storage Devices'],
            ['name' => 'QNAP TS-251D NAS', 'model' => 'TS-251D', 'brand' => 'QNAP', 'category' => 'Storage Devices'],
            ['name' => 'Seagate IronWolf 4TB NAS HDD', 'model' => 'ST4000VN008', 'brand' => 'Seagate', 'category' => 'Storage Devices'],
            ['name' => 'Samsung T7 Portable SSD 1TB', 'model' => 'MU-PC1T0T', 'brand' => 'Samsung', 'category' => 'Storage Devices'],
            ['name' => 'Crucial MX500 2TB SSD', 'model' => 'CT2000MX500SSD1', 'brand' => 'Crucial', 'category' => 'Storage Devices'],
            ['name' => 'Kingston DataTraveler Max 1TB USB', 'model' => 'DTMAXA/1TB', 'brand' => 'Kingston', 'category' => 'Storage Devices'],
            ['name' => 'LaCie Rugged 2TB External HDD', 'model' => 'STHR2000800', 'brand' => 'LaCie', 'category' => 'Storage Devices'],

            // Software Licenses
            ['name' => 'Microsoft Office 365 Business Premium', 'model' => 'E3 License', 'brand' => 'Microsoft', 'category' => 'Software Licenses'],
            ['name' => 'Adobe Creative Cloud All Apps', 'model' => 'CC All Apps', 'brand' => 'Adobe', 'category' => 'Software Licenses'],
            ['name' => 'Windows 10 Enterprise License', 'model' => 'Win10 Ent', 'brand' => 'Microsoft', 'category' => 'Software Licenses'],
            ['name' => 'Windows Server 2019 Standard', 'model' => 'WS2019 Std', 'brand' => 'Microsoft', 'category' => 'Software Licenses'],
            ['name' => 'VMware vSphere Enterprise Plus', 'model' => 'vSphere EP', 'brand' => 'VMware', 'category' => 'Software Licenses'],
            ['name' => 'Autodesk AutoCAD 2023', 'model' => 'AutoCAD 2023', 'brand' => 'Autodesk', 'category' => 'Software Licenses'],
            ['name' => 'Norton 360 Deluxe Antivirus', 'model' => '360 Deluxe', 'brand' => 'Norton', 'category' => 'Software Licenses'],
            ['name' => 'McAfee Total Protection', 'model' => 'Total Protection', 'brand' => 'McAfee', 'category' => 'Software Licenses'],

            // Additional Network Equipment
            ['name' => 'Fortinet FortiGate 60E Firewall', 'model' => 'FG-60E', 'brand' => 'Fortinet', 'category' => 'Network Equipment'],
            ['name' => 'SonicWall TZ370 Firewall', 'model' => 'TZ370', 'brand' => 'SonicWall', 'category' => 'Network Equipment'],
            ['name' => 'Palo Alto PA-220 Firewall', 'model' => 'PA-220', 'brand' => 'Palo Alto', 'category' => 'Network Equipment'],
            ['name' => 'WatchGuard Firebox T35', 'model' => 'T35', 'brand' => 'WatchGuard', 'category' => 'Network Equipment'],
            ['name' => 'Aruba Instant On AP22', 'model' => 'AP22', 'brand' => 'Aruba', 'category' => 'Network Equipment'],
            ['name' => 'Meraki MR36 Access Point', 'model' => 'MR36', 'brand' => 'Cisco Meraki', 'category' => 'Network Equipment'],
            ['name' => 'Ruckus R750 Access Point', 'model' => 'R750', 'brand' => 'Ruckus', 'category' => 'Network Equipment'],
            ['name' => 'Ubiquiti EdgeRouter X', 'model' => 'ER-X', 'brand' => 'Ubiquiti', 'category' => 'Network Equipment'],

            // Additional Mobile Devices
            ['name' => 'iPhone 12 Pro Max', 'model' => 'A2342', 'brand' => 'Apple', 'category' => 'Mobile Devices'],
            ['name' => 'Samsung Galaxy Note 20 Ultra', 'model' => 'SM-N986B', 'brand' => 'Samsung', 'category' => 'Mobile Devices'],
            ['name' => 'iPad Air 4th Gen', 'model' => 'A2316', 'brand' => 'Apple', 'category' => 'Mobile Devices'],
            ['name' => 'Samsung Galaxy Tab A7', 'model' => 'SM-T505', 'brand' => 'Samsung', 'category' => 'Mobile Devices'],
            ['name' => 'Google Pixel 5', 'model' => 'GD1YQ', 'brand' => 'Google', 'category' => 'Mobile Devices'],
            ['name' => 'OnePlus 8T', 'model' => 'KB2003', 'brand' => 'OnePlus', 'category' => 'Mobile Devices'],
            ['name' => 'Huawei Mate 40 Pro', 'model' => 'ELS-NX9', 'brand' => 'Huawei', 'category' => 'Mobile Devices'],
            ['name' => 'Xiaomi Redmi Note 10 Pro', 'model' => 'M2101K6G', 'brand' => 'Xiaomi', 'category' => 'Mobile Devices'],
        ];

        // Get additional category IDs
        $networkCategory = AssetCategory::where('name', 'Network Equipment')->first();
        $mobileCategory = AssetCategory::where('name', 'Mobile Devices')->first();
        $officeCategory = AssetCategory::where('name', 'Office Equipment')->first();
        $storageCategory = AssetCategory::where('name', 'Storage Devices')->first();
        $softwareCategory = AssetCategory::where('name', 'Software Licenses')->first();

        $categoryMap = [
            'Network Equipment' => $networkCategory,
            'Mobile Devices' => $mobileCategory,
            'Office Equipment' => $officeCategory,
            'Storage Devices' => $storageCategory,
            'Software Licenses' => $softwareCategory,
        ];

        // Create standalone assets
        foreach ($standaloneAssets as $assetInfo) {
            $category = $categoryMap[$assetInfo['category']];
            if ($category) {
                $asset = $this->createAsset($assetInfo, $category->id, $vendors, $departments, $users, $assetColumns);
                $createdAssets[] = $asset;
            }
        }

        // Create additional random standalone assets to reach 50 total
        $remainingCount = 50 - count($standaloneAssets);
        $additionalCategories = array_filter($categoryMap);
        
        for ($i = 0; $i < $remainingCount; $i++) {
            $category = $additionalCategories[array_rand($additionalCategories)];
            $randomAsset = $this->generateRandomStandaloneAsset($category->name);
            
            $asset = $this->createAsset($randomAsset, $category->id, $vendors, $departments, $users, $assetColumns);
            $createdAssets[] = $asset;
        }

            $this->command->info("Successfully created " . count($createdAssets) . " assets total!");
            $this->command->info("- 50 assets with related records (computers, monitors, printers, peripherals)");
            $this->command->info("- 50 standalone assets (network equipment, mobile devices, office equipment, storage, software)");
            
        } catch (\Exception $e) {
            $this->command->error('Asset Seeder failed: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function createAsset($data, $categoryId, $vendors, $departments, $users, $assetColumns)
    {
        $vendor = $vendors->random();
        $department = $departments->random();
        $user = $users->isNotEmpty() ? $users->random() : null;
        
        $assetData = [
            'asset_tag' => $this->generateAssetTag($categoryId),
            'category_id' => $categoryId,
            'vendor_id' => $vendor->id,
            'name' => $data['name'],
            'description' => $this->generateDescription($data),
            'serial_number' => $this->generateSerialNumber($data['brand']),
            'purchase_date' => Carbon::now()->subDays(rand(30, 730)),
            'warranty_end' => Carbon::now()->addYears(rand(1, 3)),
            'cost' => $this->getRandomCost($data['name']),
            'po_number' => 'PO-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'entity' => ['MIDC', 'PHILTOWER', 'PRIMUS'][array_rand(['MIDC', 'PHILTOWER', 'PRIMUS'])],
            'lifespan' => rand(3, 7),
            'location' => $this->getRandomLocation(),
            'notes' => $this->generateNotes($data),
            'status' => $this->getRandomStatus(),
            'movement' => $this->getRandomMovement(),
            'assigned_to' => ($user && rand(0, 1)) ? $user->id : null,
            'assigned_date' => ($user && rand(0, 1)) ? Carbon::now()->subDays(rand(1, 180)) : null,
            'department_id' => $department->id,
        ];

        // Add optional fields if they exist in the database
        if (in_array('model', $assetColumns)) {
            $assetData['model'] = $data['model'];
        }

        return Asset::create($assetData);
    }

    private function generateAssetTag($categoryId)
    {
        // Get category name for more accurate prefixes
        $category = AssetCategory::find($categoryId);
        $categoryName = $category ? $category->name : 'Unknown';
        
        $prefixes = [
            'Computer Hardware' => 'PC',
            'Monitors' => 'MON',
            'Printers' => 'PRT',
            'Peripherals' => 'PER',
            'Network Equipment' => 'NET',
            'Mobile Devices' => 'MOB',
            'Office Equipment' => 'OFF',
            'Software Licenses' => 'SW',
            'Storage Devices' => 'STO',
        ];
        
        $prefix = $prefixes[$categoryName] ?? 'AST';
        return $prefix . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function generateDescription($data)
    {
        $descriptions = [
            'High-performance business workstation',
            'Professional-grade equipment',
            'Reliable office solution',
            'Enterprise-class hardware',
            'Advanced technology device',
            'Premium quality equipment',
            'Modern business solution',
            'Professional workstation',
        ];
        
        return $descriptions[array_rand($descriptions)] . ' - ' . $data['brand'] . ' ' . $data['model'];
    }

    private function generateSerialNumber($brand)
    {
        $brandPrefixes = [
            'Dell' => 'DL',
            'HP' => 'HP',
            'Lenovo' => 'LV',
            'Canon' => 'CN',
            'Brother' => 'BR',
            'Logitech' => 'LG',
            'Microsoft' => 'MS',
            'Razer' => 'RZ',
            'Corsair' => 'CR',
            'SteelSeries' => 'SS',
            'Blue' => 'BL',
            'Anker' => 'AK',
            'SanDisk' => 'SD',
            'Belkin' => 'BK',
            'HyperX' => 'HX',
        ];
        
        $prefix = $brandPrefixes[$brand] ?? 'SN';
        return $prefix . '-' . strtoupper(substr(md5(uniqid()), 0, 10));
    }

    private function generateNotes($data)
    {
        $notes = [
            'Includes original packaging and documentation',
            'Warranty registration completed',
            'Asset tagged and inventoried',
            'Ready for deployment',
            'Passed quality inspection',
            'Includes all accessories',
            'Configured for office use',
            'Maintenance schedule established',
        ];
        
        return rand(0, 1) ? $notes[array_rand($notes)] : null;
    }

    private function getRandomCost($name)
    {
        // Realistic cost ranges based on device type
        if (strpos($name, 'Computer') !== false || strpos($name, 'OptiPlex') !== false || strpos($name, 'EliteDesk') !== false || strpos($name, 'ThinkCentre') !== false || strpos($name, 'Precision') !== false) {
            return rand(800, 2500);
        } elseif (strpos($name, 'Monitor') !== false || strpos($name, 'UltraSharp') !== false || strpos($name, 'EliteDisplay') !== false || strpos($name, 'ThinkVision') !== false) {
            return rand(200, 800);
        } elseif (strpos($name, 'Printer') !== false || strpos($name, 'LaserJet') !== false || strpos($name, 'imageCLASS') !== false || strpos($name, 'OfficeJet') !== false) {
            return rand(150, 800);
        } elseif (strpos($name, 'Switch') !== false || strpos($name, 'Router') !== false || strpos($name, 'Firewall') !== false) {
            return rand(200, 2000);
        } elseif (strpos($name, 'iPhone') !== false || strpos($name, 'iPad') !== false || strpos($name, 'Galaxy') !== false) {
            return rand(300, 1500);
        } elseif (strpos($name, 'Projector') !== false || strpos($name, 'Whiteboard') !== false || strpos($name, 'Video Bar') !== false) {
            return rand(500, 3000);
        } elseif (strpos($name, 'NAS') !== false || strpos($name, 'SSD') !== false || strpos($name, 'External') !== false) {
            return rand(100, 800);
        } elseif (strpos($name, 'License') !== false || strpos($name, 'Software') !== false || strpos($name, 'Office') !== false || strpos($name, 'Creative') !== false) {
            return rand(50, 500);
        } else {
            return rand(25, 300);
        }
    }

    private function getRandomLocation()
    {
        $locations = [
            'Office Floor 1',
            'Office Floor 2',
            'Office Floor 3',
            'IT Department',
            'Conference Room A',
            'Conference Room B',
            'Storage Room',
            'Reception Area',
            'Break Room',
            'Training Room',
        ];
        
        return $locations[array_rand($locations)];
    }

    private function getRandomStatus()
    {
        $statuses = ['Active', 'Active', 'Active', 'Active', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomMovement()
    {
        $movements = ['Deployed Tagged', 'Deployed Tagged', 'Deployed Tagged', 'Deployed', 'New Arrival', 'Returned'];
        return $movements[array_rand($movements)];
    }

    // Computer-specific methods
    private function getRandomProcessor()
    {
        $processors = [
            'Intel Core i5-10400',
            'Intel Core i7-10700',
            'Intel Core i9-10900',
            'AMD Ryzen 5 3600',
            'AMD Ryzen 7 3700X',
            'Intel Core i5-11400',
            'Intel Core i7-11700',
            'AMD Ryzen 5 5600X',
        ];
        return $processors[array_rand($processors)];
    }

    private function getRandomMemory()
    {
        $memory = ['8GB DDR4', '16GB DDR4', '32GB DDR4', '8GB DDR5', '16GB DDR5'];
        return $memory[array_rand($memory)];
    }

    private function getRandomStorage()
    {
        $storage = [
            '256GB SSD',
            '512GB SSD',
            '1TB SSD',
            '256GB NVMe SSD',
            '512GB NVMe SSD',
            '1TB NVMe SSD',
            '2TB HDD',
            '1TB HDD + 256GB SSD',
        ];
        return $storage[array_rand($storage)];
    }

    private function getRandomOS()
    {
        $os = ['Windows 10 Pro', 'Windows 11 Pro', 'Windows 10 Enterprise', 'Windows 11 Enterprise'];
        return $os[array_rand($os)];
    }

    private function getRandomGraphicsCard()
    {
        $graphics = [
            'Intel UHD Graphics 630',
            'Intel UHD Graphics 750',
            'NVIDIA GeForce GTX 1650',
            'NVIDIA GeForce RTX 3060',
            'AMD Radeon RX 5500 XT',
            'Integrated Graphics',
        ];
        return $graphics[array_rand($graphics)];
    }

    private function getRandomComputerType()
    {
        $types = ['Desktop', 'Desktop', 'Desktop', 'Laptop', 'Workstation', 'Server'];
        return $types[array_rand($types)];
    }

    // Monitor-specific methods
    private function getRandomMonitorSize()
    {
        $sizes = ['24"', '24"', '27"', '27"', '32"', '22"'];
        return $sizes[array_rand($sizes)];
    }

    private function getRandomResolution()
    {
        $resolutions = [
            '1920x1080 (Full HD)',
            '1920x1080 (Full HD)',
            '2560x1440 (QHD)',
            '3840x2160 (4K)',
            '1680x1050 (WSXGA+)',
        ];
        return $resolutions[array_rand($resolutions)];
    }

    private function getRandomPanelType()
    {
        $panels = ['IPS', 'IPS', 'VA', 'TN', 'OLED'];
        return $panels[array_rand($panels)];
    }

    // Printer-specific methods
    private function getRandomPrinterType()
    {
        $types = ['Laser', 'Laser', 'Inkjet', 'Multifunction', 'Laser Multifunction'];
        return $types[array_rand($types)];
    }

    // Peripheral-specific methods
    private function getRandomPeripheralType($name)
    {
        if (strpos($name, 'Mouse') !== false || strpos($name, 'MX Master') !== false || strpos($name, 'Rival') !== false || strpos($name, 'Harpoon') !== false) {
            return 'Mouse';
        } elseif (strpos($name, 'Keyboard') !== false || strpos($name, 'BlackWidow') !== false || strpos($name, 'K95') !== false || strpos($name, 'MX Keys') !== false) {
            return 'Keyboard';
        } elseif (strpos($name, 'Webcam') !== false || strpos($name, 'C920') !== false || strpos($name, 'Brio') !== false) {
            return 'Webcam';
        } elseif (strpos($name, 'Headset') !== false || strpos($name, 'Cloud') !== false) {
            return 'Headset';
        } elseif (strpos($name, 'Speaker') !== false || strpos($name, 'Z623') !== false) {
            return 'Speaker';
        } elseif (strpos($name, 'Microphone') !== false || strpos($name, 'Yeti') !== false) {
            return 'Microphone';
        } elseif (strpos($name, 'Hub') !== false || strpos($name, 'PowerExpand') !== false) {
            return 'USB Hub';
        } elseif (strpos($name, 'USB') !== false || strpos($name, 'Extreme Pro') !== false) {
            return 'External Drive';
        } else {
            return 'Other';
        }
    }

    private function getRandomInterface($name)
    {
        if (strpos($name, 'Wireless') !== false || strpos($name, 'MX Master') !== false || strpos($name, 'MX Keys') !== false) {
            return 'Wireless';
        } elseif (strpos($name, 'Bluetooth') !== false) {
            return 'Bluetooth';
        } elseif (strpos($name, 'USB-C') !== false || strpos($name, 'Hub') !== false) {
            return 'USB-C';
        } elseif (strpos($name, 'Lightning') !== false) {
            return 'Lightning';
        } else {
            $interfaces = ['USB', 'USB', 'USB', 'Bluetooth', 'Wireless'];
            return $interfaces[array_rand($interfaces)];
        }
    }

    private function generateRandomStandaloneAsset($categoryName)
    {
        $randomAssets = [
            'Network Equipment' => [
                ['name' => 'Cisco Catalyst 9200 Switch', 'model' => 'C9200-48P', 'brand' => 'Cisco'],
                ['name' => 'Netgear GS108T Switch', 'model' => 'GS108T', 'brand' => 'Netgear'],
                ['name' => 'TP-Link TL-SG1024D Switch', 'model' => 'TL-SG1024D', 'brand' => 'TP-Link'],
                ['name' => 'D-Link DIR-882 Router', 'model' => 'DIR-882', 'brand' => 'D-Link'],
                ['name' => 'Ubiquiti UniFi Switch 24', 'model' => 'US-24', 'brand' => 'Ubiquiti'],
                ['name' => 'Fortinet FortiAP 221C', 'model' => 'FAP-221C', 'brand' => 'Fortinet'],
                ['name' => 'Ruckus ICX 7150 Switch', 'model' => 'ICX-7150-24P', 'brand' => 'Ruckus'],
                ['name' => 'Aruba 2530 Switch', 'model' => 'J9772A', 'brand' => 'Aruba'],
            ],
            'Mobile Devices' => [
                ['name' => 'iPhone SE 3rd Gen', 'model' => 'A2595', 'brand' => 'Apple'],
                ['name' => 'Samsung Galaxy A52 5G', 'model' => 'SM-A526B', 'brand' => 'Samsung'],
                ['name' => 'iPad Mini 6th Gen', 'model' => 'A2567', 'brand' => 'Apple'],
                ['name' => 'Google Pixel 4a', 'model' => 'G025E', 'brand' => 'Google'],
                ['name' => 'OnePlus Nord 2', 'model' => 'DN2103', 'brand' => 'OnePlus'],
                ['name' => 'Huawei P30 Pro', 'model' => 'VOG-L29', 'brand' => 'Huawei'],
                ['name' => 'Xiaomi Mi 10T Pro', 'model' => 'M2007J3SG', 'brand' => 'Xiaomi'],
                ['name' => 'Sony Xperia 1 III', 'model' => 'XQ-BC52', 'brand' => 'Sony'],
            ],
            'Office Equipment' => [
                ['name' => 'Epson EB-X41 Projector', 'model' => 'EB-X41', 'brand' => 'Epson'],
                ['name' => 'BenQ MW632ST Projector', 'model' => 'MW632ST', 'brand' => 'BenQ'],
                ['name' => 'Optoma HD146X Projector', 'model' => 'HD146X', 'brand' => 'Optoma'],
                ['name' => 'ViewSonic PJD7720HD Projector', 'model' => 'PJD7720HD', 'brand' => 'ViewSonic'],
                ['name' => 'Logitech MeetUp ConferenceCam', 'model' => 'MeetUp', 'brand' => 'Logitech'],
                ['name' => 'Poly Studio P5 Video Bar', 'model' => 'Studio P5', 'brand' => 'Poly'],
                ['name' => 'Jabra PanaCast 50 Video Bar', 'model' => 'PanaCast 50', 'brand' => 'Jabra'],
                ['name' => 'Yealink MeetingBar A20', 'model' => 'MeetingBar A20', 'brand' => 'Yealink'],
            ],
            'Storage Devices' => [
                ['name' => 'WD Elements 2TB External HDD', 'model' => 'WDBU6Y0020BBK', 'brand' => 'Western Digital'],
                ['name' => 'Seagate Expansion 4TB External HDD', 'model' => 'STEB4000400', 'brand' => 'Seagate'],
                ['name' => 'Toshiba Canvio Basics 1TB HDD', 'model' => 'HDTB410XK3AA', 'brand' => 'Toshiba'],
                ['name' => 'LaCie Porsche Design 2TB HDD', 'model' => 'STHR2000400', 'brand' => 'LaCie'],
                ['name' => 'Samsung Portable SSD T5 500GB', 'model' => 'MU-PA500B', 'brand' => 'Samsung'],
                ['name' => 'SanDisk Extreme Portable SSD 1TB', 'model' => 'SDSSDE60-1T00', 'brand' => 'SanDisk'],
                ['name' => 'Kingston XS2000 Portable SSD 500GB', 'model' => 'SXS2000/500G', 'brand' => 'Kingston'],
                ['name' => 'Crucial X8 Portable SSD 1TB', 'model' => 'CT1000X8SSD9', 'brand' => 'Crucial'],
            ],
            'Software Licenses' => [
                ['name' => 'Microsoft Visio Professional 2021', 'model' => 'Visio Pro 2021', 'brand' => 'Microsoft'],
                ['name' => 'Adobe Acrobat Pro DC', 'model' => 'Acrobat Pro DC', 'brand' => 'Adobe'],
                ['name' => 'SketchUp Pro 2022', 'model' => 'SketchUp Pro', 'brand' => 'SketchUp'],
                ['name' => 'CorelDRAW Graphics Suite 2022', 'model' => 'CorelDRAW 2022', 'brand' => 'Corel'],
                ['name' => 'SolidWorks Professional 2022', 'model' => 'SW Professional', 'brand' => 'Dassault'],
                ['name' => 'MATLAB R2022a', 'model' => 'MATLAB R2022a', 'brand' => 'MathWorks'],
                ['name' => 'Tableau Desktop Professional', 'model' => 'Tableau Desktop', 'brand' => 'Tableau'],
                ['name' => 'Slack Business+', 'model' => 'Slack Business+', 'brand' => 'Slack'],
            ],
        ];

        $categoryAssets = $randomAssets[$categoryName] ?? [];
        return $categoryAssets[array_rand($categoryAssets)];
    }

    private function createDefaultVendors()
    {
        $defaultVendors = [
            [
                'name' => 'Dell Technologies',
                'contact_person' => 'John Smith',
                'email' => 'sales@dell.com',
                'phone' => '+1-800-DELL-123',
                'address' => 'One Dell Way, Round Rock, TX 78682, USA',
            ],
            [
                'name' => 'HP Inc.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sales@hp.com',
                'phone' => '+1-800-HP-INVENT',
                'address' => '1501 Page Mill Rd, Palo Alto, CA 94304, USA',
            ],
            [
                'name' => 'Lenovo Group Ltd.',
                'contact_person' => 'Michael Chen',
                'email' => 'sales@lenovo.com',
                'phone' => '+1-855-2-LENOVO',
                'address' => '1009 Think Place, Morrisville, NC 27560, USA',
            ],
            [
                'name' => 'Apple Inc.',
                'contact_person' => 'Lisa Anderson',
                'email' => 'sales@apple.com',
                'phone' => '+1-800-APL-CARE',
                'address' => '1 Apple Park Way, Cupertino, CA 95014, USA',
            ],
            [
                'name' => 'Samsung Electronics',
                'contact_person' => 'David Kim',
                'email' => 'sales@samsung.com',
                'phone' => '+1-800-SAMSUNG',
                'address' => '85 Challenger Rd, Ridgefield Park, NJ 07660, USA',
            ],
            [
                'name' => 'Cisco Systems',
                'contact_person' => 'Robert Wilson',
                'email' => 'sales@cisco.com',
                'phone' => '+1-800-553-6387',
                'address' => '170 West Tasman Dr, San Jose, CA 95134, USA',
            ],
            [
                'name' => 'Microsoft Corporation',
                'contact_person' => 'Jennifer Brown',
                'email' => 'sales@microsoft.com',
                'phone' => '+1-800-MICROSOFT',
                'address' => 'One Microsoft Way, Redmond, WA 98052, USA',
            ],
            [
                'name' => 'Canon Inc.',
                'contact_person' => 'Yuki Tanaka',
                'email' => 'sales@canon.com',
                'phone' => '+1-800-OK-CANON',
                'address' => '1 Canon Park, Melville, NY 11747, USA',
            ],
            [
                'name' => 'Logitech International',
                'contact_person' => 'Maria Garcia',
                'email' => 'sales@logitech.com',
                'phone' => '+1-646-454-3200',
                'address' => '7700 Gateway Blvd, Newark, CA 94560, USA',
            ],
            [
                'name' => 'Netgear Inc.',
                'contact_person' => 'Alex Thompson',
                'email' => 'sales@netgear.com',
                'phone' => '+1-888-NETGEAR',
                'address' => '350 E. Plumeria Dr, San Jose, CA 95134, USA',
            ],
        ];

        $createdVendors = collect();
        
        foreach ($defaultVendors as $vendorData) {
            $vendor = Vendor::create($vendorData);
            $createdVendors->push($vendor);
            $this->command->info("Created vendor: {$vendor->name}");
        }

        $this->command->info("Successfully created {$createdVendors->count()} default vendors.");
        return $createdVendors;
    }

    private function createDefaultDepartments()
    {
        $defaultDepartments = [
            [
                'name' => 'Information Technology',
                'description' => 'IT Department responsible for technology infrastructure and support',
                'code' => 'IT',
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Human Resources department managing personnel and policies',
                'code' => 'HR',
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance department handling accounting and financial operations',
                'code' => 'FIN',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Marketing department responsible for brand promotion and sales',
                'code' => 'MKT',
            ],
            [
                'name' => 'Operations',
                'description' => 'Operations department managing daily business operations',
                'code' => 'OPS',
            ],
        ];

        $createdDepartments = collect();
        
        foreach ($defaultDepartments as $departmentData) {
            $department = Department::create($departmentData);
            $createdDepartments->push($department);
            $this->command->info("Created department: {$department->name}");
        }

        $this->command->info("Successfully created {$createdDepartments->count()} default departments.");
        return $createdDepartments;
    }

    private function createDefaultCategories()
    {
        $defaultCategories = [
            [
                'name' => 'Computer Hardware',
                'description' => 'Desktop computers, laptops, servers, and internal computer components (RAM, CPU, motherboards, etc.)'
            ],
            [
                'name' => 'Monitors',
                'description' => 'Computer monitors, displays, and screen-related equipment'
            ],
            [
                'name' => 'Printers',
                'description' => 'Printers, scanners, multifunction devices, and printing equipment'
            ],
            [
                'name' => 'Peripherals',
                'description' => 'External devices like keyboards, mice, webcams, speakers, and other computer accessories'
            ],
            [
                'name' => 'Network Equipment',
                'description' => 'Routers, switches, access points, cables, and networking hardware'
            ],
            [
                'name' => 'Mobile Devices',
                'description' => 'Smartphones, tablets, mobile hotspots, and portable devices'
            ],
            [
                'name' => 'Office Equipment',
                'description' => 'Furniture, projectors, whiteboards, and general office equipment'
            ],
            [
                'name' => 'Software Licenses',
                'description' => 'Software licenses, subscriptions, and digital assets'
            ],
            [
                'name' => 'Storage Devices',
                'description' => 'External hard drives, USB drives, NAS devices, and storage equipment'
            ],
        ];

        $createdCategories = collect();
        
        foreach ($defaultCategories as $categoryData) {
            $category = AssetCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
            $createdCategories->push($category);
            $this->command->info("Created/found category: {$category->name}");
        }

        $this->command->info("Successfully processed {$createdCategories->count()} categories.");
        return $createdCategories;
    }
}
