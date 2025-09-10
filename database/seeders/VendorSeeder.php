<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Dell Technologies',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@dell.com',
                'phone' => '+1-800-555-3355',
                'address' => '1 Dell Way, Round Rock, TX 78682'
            ],
            [
                'name' => 'HP Inc.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah.johnson@hp.com',
                'phone' => '+1-800-555-4747',
                'address' => '1501 Page Mill Road, Palo Alto, CA 94304'
            ],
            [
                'name' => 'Lenovo Group',
                'contact_person' => 'Michael Chen',
                'email' => 'michael.chen@lenovo.com',
                'phone' => '+1-855-253-6686',
                'address' => '8001 Development Dr, Morrisville, NC 27560'
            ],
            [
                'name' => 'Canon Inc.',
                'contact_person' => 'Lisa Williams',
                'email' => 'lisa.williams@canon.com',
                'phone' => '+1-800-652-2666',
                'address' => '1 Canon Park, Melville, NY 11747'
            ],
            [
                'name' => 'Cisco Systems',
                'contact_person' => 'Robert Davis',
                'email' => 'robert.davis@cisco.com',
                'phone' => '+1-408-526-4000',
                'address' => '170 West Tasman Dr, San Jose, CA 95134'
            ],
            [
                'name' => 'Logitech International',
                'contact_person' => 'Emma Rodriguez',
                'email' => 'emma.rodriguez@logitech.com',
                'phone' => '+1-510-795-8500',
                'address' => '7700 Gateway Blvd, Newark, CA 94560'
            ],
            [
                'name' => 'ASUS Computer International',
                'contact_person' => 'David Chang',
                'email' => 'david.chang@asus.com',
                'phone' => '+1-812-282-2787',
                'address' => '800 Corporate Way, Fremont, CA 94539'
            ],
            [
                'name' => 'Acer America Corporation',
                'contact_person' => 'Jennifer Liu',
                'email' => 'jennifer.liu@acer.com',
                'phone' => '+1-254-298-4000',
                'address' => '333 West San Carlos St, San Jose, CA 95110'
            ],
            [
                'name' => 'Samsung Electronics',
                'contact_person' => 'Kevin Park',
                'email' => 'kevin.park@samsung.com',
                'phone' => '+1-972-761-7000',
                'address' => '85 Challenger Rd, Ridgefield Park, NJ 07660'
            ],
            [
                'name' => 'LG Electronics USA',
                'contact_person' => 'Maria Gonzalez',
                'email' => 'maria.gonzalez@lge.com',
                'phone' => '+1-201-816-2000',
                'address' => '1000 Sylvan Ave, Englewood Cliffs, NJ 07632'
            ],
            [
                'name' => 'Brother International',
                'contact_person' => 'Thomas Anderson',
                'email' => 'thomas.anderson@brother.com',
                'phone' => '+1-908-704-1700',
                'address' => '200 Crossing Blvd, Bridgewater, NJ 08807'
            ],
            [
                'name' => 'Epson America Inc.',
                'contact_person' => 'Rachel Kim',
                'email' => 'rachel.kim@epson.com',
                'phone' => '+1-562-981-3840',
                'address' => '3840 Kilroy Airport Way, Long Beach, CA 90806'
            ],
            [
                'name' => 'Microsoft Corporation',
                'contact_person' => 'Steve Wilson',
                'email' => 'steve.wilson@microsoft.com',
                'phone' => '+1-425-882-8080',
                'address' => '1 Microsoft Way, Redmond, WA 98052'
            ],
            [
                'name' => 'Apple Inc.',
                'contact_person' => 'Jessica Taylor',
                'email' => 'jessica.taylor@apple.com',
                'phone' => '+1-408-996-1010',
                'address' => '1 Apple Park Way, Cupertino, CA 95014'
            ],
            [
                'name' => 'Razer Inc.',
                'contact_person' => 'Alex Chen',
                'email' => 'alex.chen@razer.com',
                'phone' => '+1-858-395-9895',
                'address' => '1 Razer Way, Carlsbad, CA 92008'
            ],
            [
                'name' => 'Corsair Gaming Inc.',
                'contact_person' => 'Mark Thompson',
                'email' => 'mark.thompson@corsair.com',
                'phone' => '+1-510-657-8747',
                'address' => '47100 Bayside Pkwy, Fremont, CA 94538'
            ]
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
