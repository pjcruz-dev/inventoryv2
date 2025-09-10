<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            [
                'asset_tag' => 'COMP001',
                'category_id' => 1, // Computer Hardware
                'vendor_id' => 1, // Dell Technologies
                'name' => 'Dell OptiPlex 7090',
                'description' => 'Desktop computer for office use',
                'serial_number' => 'DL7090001',
                'purchase_date' => '2024-01-15',
                'warranty_end' => '2027-01-15',
                'cost' => 1299.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'COMP002',
                'category_id' => 1, // Computer Hardware
                'vendor_id' => 2, // HP Inc.
                'name' => 'HP EliteBook 850',
                'description' => 'Business laptop for mobile work',
                'serial_number' => 'HP850002',
                'purchase_date' => '2024-02-20',
                'warranty_end' => '2027-02-20',
                'cost' => 1599.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON001',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 1, // Dell Technologies
                'name' => 'Dell UltraSharp U2720Q',
                'description' => '27-inch 4K monitor',
                'serial_number' => 'DLU2720001',
                'purchase_date' => '2024-01-15',
                'warranty_end' => '2027-01-15',
                'cost' => 599.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT001',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 4, // Canon Inc.
                'name' => 'Canon imageRUNNER ADVANCE',
                'description' => 'Multifunction printer for office use',
                'serial_number' => 'CN2025001',
                'purchase_date' => '2024-03-10',
                'warranty_end' => '2026-03-10',
                'cost' => 2499.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'NET001',
                'category_id' => 5, // Network Equipment
                'vendor_id' => 5, // Cisco Systems
                'name' => 'Cisco Catalyst 2960-X',
                'description' => '24-port network switch',
                'serial_number' => 'CS2960001',
                'purchase_date' => '2024-01-05',
                'warranty_end' => '2029-01-05',
                'cost' => 899.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'LAP001',
                'category_id' => 1, // Computer Hardware
                'vendor_id' => 3, // Lenovo Group
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'description' => 'Ultra-portable business laptop',
                'serial_number' => 'LN-X1C001',
                'purchase_date' => '2024-04-15',
                'warranty_end' => '2027-04-15',
                'cost' => 1899.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'LAP002',
                'category_id' => 1, // Computer Hardware
                'vendor_id' => 2, // HP Inc.
                'name' => 'HP ZBook Studio G9',
                'description' => 'Mobile workstation laptop',
                'serial_number' => 'HP-ZB002',
                'purchase_date' => '2024-04-20',
                'warranty_end' => '2027-04-20',
                'cost' => 2299.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'DSK001',
                'category_id' => 1, // Computer Hardware
                'vendor_id' => 1, // Dell Technologies
                'name' => 'Dell Precision 3660 Tower',
                'description' => 'High-performance desktop workstation',
                'serial_number' => 'DL-P3660001',
                'purchase_date' => '2024-05-01',
                'warranty_end' => '2027-05-01',
                'cost' => 1799.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'DSK002',
                'category_id' => 1, // Computer Hardware
                'vendor_id' => 2, // HP Inc.
                'name' => 'HP EliteDesk 800 G9',
                'description' => 'Compact desktop computer',
                'serial_number' => 'HP-ED800002',
                'purchase_date' => '2024-05-05',
                'warranty_end' => '2027-05-05',
                'cost' => 1199.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON002',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 2, // HP Inc.
                'name' => 'HP E24 G5 Monitor',
                'description' => '24-inch Full HD business monitor',
                'serial_number' => 'HP-E24002',
                'purchase_date' => '2024-05-10',
                'warranty_end' => '2027-05-10',
                'cost' => 299.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON003',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 3, // Lenovo Group
                'name' => 'Lenovo ThinkVision P27h-20',
                'description' => '27-inch QHD USB-C monitor',
                'serial_number' => 'LN-TV003',
                'purchase_date' => '2024-05-12',
                'warranty_end' => '2027-05-12',
                'cost' => 449.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT002',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 2, // HP Inc.
                'name' => 'HP LaserJet Pro M404dn',
                'description' => 'Monochrome laser printer',
                'serial_number' => 'HP-LJ002',
                'purchase_date' => '2024-05-15',
                'warranty_end' => '2026-05-15',
                'cost' => 399.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT003',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 4, // Canon Inc.
                'name' => 'Canon PIXMA TR8620',
                'description' => 'All-in-one wireless inkjet printer',
                'serial_number' => 'CN-PX003',
                'purchase_date' => '2024-05-18',
                'warranty_end' => '2026-05-18',
                'cost' => 199.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU001',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech International
                'name' => 'Logitech MX Master 3S',
                'description' => 'Wireless performance mouse',
                'serial_number' => 'LG-MX001',
                'purchase_date' => '2024-05-20',
                'warranty_end' => '2026-05-20',
                'cost' => 99.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY001',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech International
                'name' => 'Logitech MX Keys Advanced',
                'description' => 'Wireless illuminated keyboard',
                'serial_number' => 'LG-MXK001',
                'purchase_date' => '2024-05-22',
                'warranty_end' => '2026-05-22',
                'cost' => 109.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'WEB001',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech International
                'name' => 'Logitech Brio 4K Webcam',
                'description' => '4K Ultra HD webcam with HDR',
                'serial_number' => 'LG-BR001',
                'purchase_date' => '2024-05-25',
                'warranty_end' => '2026-05-25',
                'cost' => 199.99,
                'status' => 'active'
            ],
            // Additional Monitor Assets
            [
                'asset_tag' => 'MON004',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 7, // ASUS
                'name' => 'ASUS ProArt PA278QV',
                'description' => '27-inch WQHD professional monitor',
                'serial_number' => 'AS-PA004',
                'purchase_date' => '2024-06-01',
                'warranty_end' => '2027-06-01',
                'cost' => 349.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON005',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 8, // Acer
                'name' => 'Acer Predator XB273K',
                'description' => '27-inch 4K gaming monitor',
                'serial_number' => 'AC-PX005',
                'purchase_date' => '2024-06-03',
                'warranty_end' => '2027-06-03',
                'cost' => 699.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON006',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 9, // Samsung
                'name' => 'Samsung Odyssey G7',
                'description' => '32-inch curved gaming monitor',
                'serial_number' => 'SM-OG006',
                'purchase_date' => '2024-06-05',
                'warranty_end' => '2027-06-05',
                'cost' => 799.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON007',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 10, // LG
                'name' => 'LG UltraWide 34WN80C',
                'description' => '34-inch ultrawide monitor',
                'serial_number' => 'LG-UW007',
                'purchase_date' => '2024-06-07',
                'warranty_end' => '2027-06-07',
                'cost' => 549.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON008',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 1, // Dell
                'name' => 'Dell S2722DC',
                'description' => '27-inch USB-C monitor',
                'serial_number' => 'DL-S27008',
                'purchase_date' => '2024-06-10',
                'warranty_end' => '2027-06-10',
                'cost' => 429.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON009',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 2, // HP
                'name' => 'HP Z27k G3 4K',
                'description' => '27-inch 4K USB-C monitor',
                'serial_number' => 'HP-Z27009',
                'purchase_date' => '2024-06-12',
                'warranty_end' => '2027-06-12',
                'cost' => 649.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON010',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 7, // ASUS
                'name' => 'ASUS TUF Gaming VG27AQ',
                'description' => '27-inch WQHD gaming monitor',
                'serial_number' => 'AS-TF010',
                'purchase_date' => '2024-06-15',
                'warranty_end' => '2027-06-15',
                'cost' => 329.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON011',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 8, // Acer
                'name' => 'Acer Nitro XV272U',
                'description' => '27-inch WQHD IPS monitor',
                'serial_number' => 'AC-NX011',
                'purchase_date' => '2024-06-18',
                'warranty_end' => '2027-06-18',
                'cost' => 299.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON012',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 9, // Samsung
                'name' => 'Samsung M7 Smart Monitor',
                'description' => '32-inch 4K smart monitor',
                'serial_number' => 'SM-M7012',
                'purchase_date' => '2024-06-20',
                'warranty_end' => '2027-06-20',
                'cost' => 399.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON013',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 10, // LG
                'name' => 'LG 27UP850-W',
                'description' => '27-inch 4K USB-C monitor',
                'serial_number' => 'LG-UP013',
                'purchase_date' => '2024-06-22',
                'warranty_end' => '2027-06-22',
                'cost' => 499.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON014',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 1, // Dell
                'name' => 'Dell P2423D',
                'description' => '24-inch QHD monitor',
                'serial_number' => 'DL-P24014',
                'purchase_date' => '2024-06-25',
                'warranty_end' => '2027-06-25',
                'cost' => 279.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON015',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 2, // HP
                'name' => 'HP EliteDisplay E243',
                'description' => '24-inch Full HD monitor',
                'serial_number' => 'HP-ED015',
                'purchase_date' => '2024-06-28',
                'warranty_end' => '2027-06-28',
                'cost' => 199.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON016',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 7, // ASUS
                'name' => 'ASUS VP28UQG',
                'description' => '28-inch 4K gaming monitor',
                'serial_number' => 'AS-VP016',
                'purchase_date' => '2024-06-30',
                'warranty_end' => '2027-06-30',
                'cost' => 349.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON017',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 8, // Acer
                'name' => 'Acer CB242Y',
                'description' => '24-inch Full HD IPS monitor',
                'serial_number' => 'AC-CB017',
                'purchase_date' => '2024-07-02',
                'warranty_end' => '2027-07-02',
                'cost' => 129.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MON018',
                'category_id' => 2, // Monitors & Displays
                'vendor_id' => 9, // Samsung
                'name' => 'Samsung CF398 Curved',
                'description' => '27-inch curved Full HD monitor',
                'serial_number' => 'SM-CF018',
                'purchase_date' => '2024-07-05',
                'warranty_end' => '2027-07-05',
                'cost' => 179.99,
                'status' => 'active'
            ],
            // Additional Printer Assets
            [
                'asset_tag' => 'PRT004',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 11, // Brother
                'name' => 'Brother HL-L3270CDW',
                'description' => 'Color laser printer with wireless',
                'serial_number' => 'BR-HL004',
                'purchase_date' => '2024-07-08',
                'warranty_end' => '2026-07-08',
                'cost' => 299.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT005',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 12, // Epson
                'name' => 'Epson EcoTank ET-4760',
                'description' => 'All-in-one supertank printer',
                'serial_number' => 'EP-ET005',
                'purchase_date' => '2024-07-10',
                'warranty_end' => '2026-07-10',
                'cost' => 399.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT006',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 2, // HP
                'name' => 'HP Color LaserJet Pro M454dw',
                'description' => 'Wireless color laser printer',
                'serial_number' => 'HP-CL006',
                'purchase_date' => '2024-07-12',
                'warranty_end' => '2026-07-12',
                'cost' => 449.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT007',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 4, // Canon
                'name' => 'Canon MAXIFY GX7020',
                'description' => 'Wireless MegaTank all-in-one printer',
                'serial_number' => 'CN-MX007',
                'purchase_date' => '2024-07-15',
                'warranty_end' => '2026-07-15',
                'cost' => 349.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT008',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 11, // Brother
                'name' => 'Brother MFC-L8900CDW',
                'description' => 'Color laser all-in-one printer',
                'serial_number' => 'BR-MF008',
                'purchase_date' => '2024-07-18',
                'warranty_end' => '2026-07-18',
                'cost' => 599.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT009',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 12, // Epson
                'name' => 'Epson WorkForce Pro WF-4830',
                'description' => 'All-in-one wireless printer',
                'serial_number' => 'EP-WF009',
                'purchase_date' => '2024-07-20',
                'warranty_end' => '2026-07-20',
                'cost' => 199.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT010',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 2, // HP
                'name' => 'HP OfficeJet Pro 9015e',
                'description' => 'All-in-one wireless printer',
                'serial_number' => 'HP-OJ010',
                'purchase_date' => '2024-07-22',
                'warranty_end' => '2026-07-22',
                'cost' => 229.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT011',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 4, // Canon
                'name' => 'Canon imageCLASS MF445dw',
                'description' => 'Monochrome laser all-in-one',
                'serial_number' => 'CN-IC011',
                'purchase_date' => '2024-07-25',
                'warranty_end' => '2026-07-25',
                'cost' => 279.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT012',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 11, // Brother
                'name' => 'Brother DCP-L2550DW',
                'description' => 'Compact monochrome laser all-in-one',
                'serial_number' => 'BR-DC012',
                'purchase_date' => '2024-07-28',
                'warranty_end' => '2026-07-28',
                'cost' => 179.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT013',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 12, // Epson
                'name' => 'Epson Expression Premium XP-7100',
                'description' => 'Small-in-one wireless printer',
                'serial_number' => 'EP-EX013',
                'purchase_date' => '2024-07-30',
                'warranty_end' => '2026-07-30',
                'cost' => 149.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT014',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 2, // HP
                'name' => 'HP LaserJet Enterprise M507dn',
                'description' => 'Monochrome laser printer',
                'serial_number' => 'HP-LE014',
                'purchase_date' => '2024-08-02',
                'warranty_end' => '2026-08-02',
                'cost' => 399.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT015',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 4, // Canon
                'name' => 'Canon SELPHY CP1300',
                'description' => 'Compact photo printer',
                'serial_number' => 'CN-SE015',
                'purchase_date' => '2024-08-05',
                'warranty_end' => '2026-08-05',
                'cost' => 129.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT016',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 11, // Brother
                'name' => 'Brother QL-820NWB',
                'description' => 'Professional label printer',
                'serial_number' => 'BR-QL016',
                'purchase_date' => '2024-08-08',
                'warranty_end' => '2026-08-08',
                'cost' => 199.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT017',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 12, // Epson
                'name' => 'Epson SureColor P400',
                'description' => 'Wide-format inkjet photo printer',
                'serial_number' => 'EP-SC017',
                'purchase_date' => '2024-08-10',
                'warranty_end' => '2026-08-10',
                'cost' => 499.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'PRT018',
                'category_id' => 3, // Printers & Scanners
                'vendor_id' => 2, // HP
                'name' => 'HP Envy 6055e',
                'description' => 'All-in-one wireless printer',
                'serial_number' => 'HP-EN018',
                'purchase_date' => '2024-08-12',
                'warranty_end' => '2026-08-12',
                'cost' => 99.99,
                'status' => 'active'
            ],
            // Additional Keyboard Assets
            [
                'asset_tag' => 'KEY002',
                'category_id' => 4, // Peripherals
                'vendor_id' => 15, // Razer
                'name' => 'Razer BlackWidow V3',
                'description' => 'Mechanical gaming keyboard',
                'serial_number' => 'RZ-BW002',
                'purchase_date' => '2024-08-15',
                'warranty_end' => '2026-08-15',
                'cost' => 139.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY003',
                'category_id' => 4, // Peripherals
                'vendor_id' => 16, // Corsair
                'name' => 'Corsair K95 RGB Platinum',
                'description' => 'Mechanical gaming keyboard with RGB',
                'serial_number' => 'CR-K95003',
                'purchase_date' => '2024-08-18',
                'warranty_end' => '2026-08-18',
                'cost' => 199.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY004',
                'category_id' => 4, // Peripherals
                'vendor_id' => 13, // Microsoft
                'name' => 'Microsoft Surface Keyboard',
                'description' => 'Wireless ultra-slim keyboard',
                'serial_number' => 'MS-SK004',
                'purchase_date' => '2024-08-20',
                'warranty_end' => '2026-08-20',
                'cost' => 99.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY005',
                'category_id' => 4, // Peripherals
                'vendor_id' => 14, // Apple
                'name' => 'Apple Magic Keyboard',
                'description' => 'Wireless keyboard for Mac',
                'serial_number' => 'AP-MK005',
                'purchase_date' => '2024-08-22',
                'warranty_end' => '2026-08-22',
                'cost' => 129.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY006',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech
                'name' => 'Logitech G915 TKL',
                'description' => 'Wireless mechanical gaming keyboard',
                'serial_number' => 'LG-G9006',
                'purchase_date' => '2024-08-25',
                'warranty_end' => '2026-08-25',
                'cost' => 229.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY007',
                'category_id' => 4, // Peripherals
                'vendor_id' => 2, // HP
                'name' => 'HP Pavilion Wireless Keyboard 600',
                'description' => 'Wireless keyboard for office use',
                'serial_number' => 'HP-PW007',
                'purchase_date' => '2024-08-28',
                'warranty_end' => '2026-08-28',
                'cost' => 39.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY008',
                'category_id' => 4, // Peripherals
                'vendor_id' => 1, // Dell
                'name' => 'Dell KB216 Wired Keyboard',
                'description' => 'Standard wired keyboard',
                'serial_number' => 'DL-KB008',
                'purchase_date' => '2024-08-30',
                'warranty_end' => '2026-08-30',
                'cost' => 19.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY009',
                'category_id' => 4, // Peripherals
                'vendor_id' => 7, // ASUS
                'name' => 'ASUS ROG Strix Scope',
                'description' => 'Mechanical gaming keyboard',
                'serial_number' => 'AS-RS009',
                'purchase_date' => '2024-09-02',
                'warranty_end' => '2026-09-02',
                'cost' => 89.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY010',
                'category_id' => 4, // Peripherals
                'vendor_id' => 3, // Lenovo
                'name' => 'Lenovo ThinkPad TrackPoint Keyboard II',
                'description' => 'Wireless keyboard with TrackPoint',
                'serial_number' => 'LN-TP010',
                'purchase_date' => '2024-09-05',
                'warranty_end' => '2026-09-05',
                'cost' => 119.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'KEY011',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech
                'name' => 'Logitech K380 Multi-Device',
                'description' => 'Compact wireless keyboard',
                'serial_number' => 'LG-K3011',
                'purchase_date' => '2024-09-08',
                'warranty_end' => '2026-09-08',
                'cost' => 39.99,
                'status' => 'active'
            ],
            // Additional Mouse Assets
            [
                'asset_tag' => 'MOU002',
                'category_id' => 4, // Peripherals
                'vendor_id' => 15, // Razer
                'name' => 'Razer DeathAdder V3',
                'description' => 'Ergonomic gaming mouse',
                'serial_number' => 'RZ-DA002',
                'purchase_date' => '2024-09-10',
                'warranty_end' => '2026-09-10',
                'cost' => 69.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU003',
                'category_id' => 4, // Peripherals
                'vendor_id' => 16, // Corsair
                'name' => 'Corsair Dark Core RGB Pro',
                'description' => 'Wireless gaming mouse',
                'serial_number' => 'CR-DC003',
                'purchase_date' => '2024-09-12',
                'warranty_end' => '2026-09-12',
                'cost' => 89.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU004',
                'category_id' => 4, // Peripherals
                'vendor_id' => 13, // Microsoft
                'name' => 'Microsoft Surface Precision Mouse',
                'description' => 'Wireless precision mouse',
                'serial_number' => 'MS-SP004',
                'purchase_date' => '2024-09-15',
                'warranty_end' => '2026-09-15',
                'cost' => 99.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU005',
                'category_id' => 4, // Peripherals
                'vendor_id' => 14, // Apple
                'name' => 'Apple Magic Mouse 2',
                'description' => 'Wireless mouse for Mac',
                'serial_number' => 'AP-MM005',
                'purchase_date' => '2024-09-18',
                'warranty_end' => '2026-09-18',
                'cost' => 79.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU006',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech
                'name' => 'Logitech G Pro X Superlight',
                'description' => 'Ultra-lightweight gaming mouse',
                'serial_number' => 'LG-GP006',
                'purchase_date' => '2024-09-20',
                'warranty_end' => '2026-09-20',
                'cost' => 149.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU007',
                'category_id' => 4, // Peripherals
                'vendor_id' => 2, // HP
                'name' => 'HP Z3700 Wireless Mouse',
                'description' => 'Slim wireless mouse',
                'serial_number' => 'HP-Z3007',
                'purchase_date' => '2024-09-22',
                'warranty_end' => '2026-09-22',
                'cost' => 29.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU008',
                'category_id' => 4, // Peripherals
                'vendor_id' => 1, // Dell
                'name' => 'Dell MS116 Optical Mouse',
                'description' => 'Basic wired optical mouse',
                'serial_number' => 'DL-MS008',
                'purchase_date' => '2024-09-25',
                'warranty_end' => '2026-09-25',
                'cost' => 12.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU009',
                'category_id' => 4, // Peripherals
                'vendor_id' => 7, // ASUS
                'name' => 'ASUS ROG Gladius III',
                'description' => 'Gaming mouse with swappable switches',
                'serial_number' => 'AS-RG009',
                'purchase_date' => '2024-09-28',
                'warranty_end' => '2026-09-28',
                'cost' => 79.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU010',
                'category_id' => 4, // Peripherals
                'vendor_id' => 3, // Lenovo
                'name' => 'Lenovo ThinkPad Bluetooth Silent Mouse',
                'description' => 'Silent wireless mouse',
                'serial_number' => 'LN-TB010',
                'purchase_date' => '2024-09-30',
                'warranty_end' => '2026-09-30',
                'cost' => 39.99,
                'status' => 'active'
            ],
            [
                'asset_tag' => 'MOU011',
                'category_id' => 4, // Peripherals
                'vendor_id' => 6, // Logitech
                'name' => 'Logitech M705 Marathon',
                'description' => 'Wireless mouse with 3-year battery',
                'serial_number' => 'LG-M7011',
                'purchase_date' => '2024-10-02',
                'warranty_end' => '2026-10-02',
                'cost' => 49.99,
                'status' => 'active'
            ]
        ];

        foreach ($assets as $asset) {
            Asset::firstOrCreate(
                ['asset_tag' => $asset['asset_tag']],
                $asset
            );
        }
    }
}
