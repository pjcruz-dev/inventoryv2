# Database Seeders

This directory contains various seeders for populating the inventory management system database with sample data.

## Available Seeders

### Essential Data Seeders
- **RoleSeeder** - Creates user roles (Super Admin, Admin, Manager, User, IT Support)
- **PermissionSeeder** - Creates system permissions
- **SystemHealthPermissionsSeeder** - Creates system health monitoring permissions
- **SecurityPermissionsSeeder** - Creates security audit permissions
- **ReportsPermissionsSeeder** - Creates reporting permissions
- **RolePermissionSeeder** - Assigns permissions to roles
- **DepartmentSeeder** - Creates organizational departments
- **AssetCategorySeeder** - Creates asset categories
- **UserSeeder** - Creates admin user account

### Sample Data Seeders
- **ComprehensiveDataSeeder** - Creates comprehensive sample data including:
  - 10 vendors (Dell, HP, Lenovo, Apple, Samsung, Canon, Epson, Cisco, Microsoft, Logitech)
  - 10 additional users with different roles and departments
  - 10 sample assets (computers, monitors, printers, peripherals, network equipment)
  - Maintenance records
  - Disposal records
  - Asset assignment confirmations

## Usage

### Run All Seeders
```bash
php artisan db:seed
```

### Run Essential Data Only
```bash
php artisan db:seed --class=EssentialDataSeeder
```

### Run Specific Seeder
```bash
php artisan db:seed --class=ComprehensiveDataSeeder
```

### Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

## Sample Data Created

### Users
- **Super Admin**: admin@gmail.com (password: 123123123)
- **10 Additional Users**: Various roles and departments

### Assets
- **Computers**: Dell OptiPlex, HP EliteBook, Lenovo ThinkPad
- **Monitors**: Dell UltraSharp 27", Samsung 24" Business
- **Printers**: Canon imageRUNNER, Epson WorkForce Pro
- **Peripherals**: Logitech MX Master 3 Mouse, K380 Keyboard
- **Network Equipment**: Cisco Catalyst Switch

### Vendors
- Dell Technologies, HP Inc., Lenovo, Apple Inc., Samsung Electronics
- Canon Philippines, Epson Philippines, Cisco Systems, Microsoft Corporation, Logitech

### Departments
- 9 parent divisions (Roll-Out, Operations & Maintenance, WHSE, Security, Commercial, External Affairs, Human Resources and Administration, Finance, Legal & Documentation)
- 32 sub-departments

### Asset Categories
- Computer Hardware, Monitors, Printers, Peripherals, Network Equipment
- Mobile Devices, Office Equipment, Software Licenses, Storage Devices, Audio/Video Equipment

## Production Considerations

For production environments, consider:
1. Comment out `ComprehensiveDataSeeder::class` in `DatabaseSeeder.php`
2. Use only `EssentialDataSeeder` for production
3. Change default passwords before deployment
4. Review and customize sample data as needed

## Customization

To customize the seeders:
1. Edit the respective seeder files
2. Modify the data arrays
3. Add new seeders as needed
4. Update the `DatabaseSeeder.php` to include new seeders
