<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'view_assets']);
        Permission::create(['name' => 'create_assets']);
        Permission::create(['name' => 'edit_assets']);
        Permission::create(['name' => 'delete_assets']);
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view_assets', 'create_assets', 'edit_assets', 'delete_assets']);
        
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo(['view_assets']);
        
        // Create test department
        $department = Department::create([
            'name' => 'IT Department',
            'description' => 'Information Technology'
        ]);
        
        // Create test vendor
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'contact_person' => 'John Doe',
            'email' => 'vendor@test.com',
            'phone' => '123-456-7890'
        ]);
        
        // Create test category
        $category = AssetCategory::create([
            'name' => 'Computers',
            'description' => 'Computer equipment'
        ]);
        
        // Create admin user
        $this->admin = User::create([
            'employee_id' => 'EMP001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'department_id' => $department->id,
            'status' => 1
        ]);
        $this->admin->assignRole('admin');
        
        // Create regular user
        $this->user = User::create([
            'employee_id' => 'EMP002',
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'department_id' => $department->id,
            'status' => 1
        ]);
        $this->user->assignRole('user');
    }

    public function test_admin_can_view_assets_index()
    {
        $this->actingAs($this->admin)
            ->get('/assets')
            ->assertStatus(200);
    }

    public function test_user_can_view_assets_index()
    {
        $this->actingAs($this->user)
            ->get('/assets')
            ->assertStatus(200);
    }

    public function test_admin_can_create_asset()
    {
        $this->actingAs($this->admin)
            ->post('/assets', [
                'asset_tag' => 'COMP-001',
                'category_id' => 1,
                'vendor_id' => 1,
                'name' => 'Test Computer',
                'description' => 'Test Description',
                'serial_number' => 'SN123456',
                'purchase_date' => '2024-01-01',
                'cost' => 1000.00,
                'po_number' => 'PO-001',
                'status' => 'Available',
                'movement' => 'New Arrival'
            ])
            ->assertRedirect('/assets');
        
        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'COMP-001',
            'name' => 'Test Computer'
        ]);
    }

    public function test_user_cannot_create_asset()
    {
        $this->actingAs($this->user)
            ->post('/assets', [
                'asset_tag' => 'COMP-002',
                'category_id' => 1,
                'vendor_id' => 1,
                'name' => 'Test Computer 2',
                'description' => 'Test Description',
                'serial_number' => 'SN123457',
                'purchase_date' => '2024-01-01',
                'cost' => 1000.00,
                'po_number' => 'PO-002',
                'status' => 'Available',
                'movement' => 'New Arrival'
            ])
            ->assertStatus(403);
    }

    public function test_asset_validation_rules()
    {
        $this->actingAs($this->admin)
            ->post('/assets', [])
            ->assertSessionHasErrors([
                'asset_tag',
                'category_id',
                'vendor_id',
                'name',
                'serial_number',
                'purchase_date',
                'cost',
                'po_number',
                'status',
                'movement'
            ]);
    }

    public function test_unique_asset_tag_validation()
    {
        // Create first asset
        Asset::create([
            'asset_tag' => 'COMP-001',
            'category_id' => 1,
            'vendor_id' => 1,
            'name' => 'Test Computer 1',
            'description' => 'Test Description',
            'serial_number' => 'SN123456',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
            'po_number' => 'PO-001',
            'status' => 'Available',
            'movement' => 'New Arrival'
        ]);

        // Try to create second asset with same tag
        $this->actingAs($this->admin)
            ->post('/assets', [
                'asset_tag' => 'COMP-001',
                'category_id' => 1,
                'vendor_id' => 1,
                'name' => 'Test Computer 2',
                'description' => 'Test Description',
                'serial_number' => 'SN123457',
                'purchase_date' => '2024-01-01',
                'cost' => 1000.00,
                'po_number' => 'PO-002',
                'status' => 'Available',
                'movement' => 'New Arrival'
            ])
            ->assertSessionHasErrors(['asset_tag']);
    }

    public function test_asset_search_functionality()
    {
        // Create test asset
        Asset::create([
            'asset_tag' => 'COMP-SEARCH-001',
            'category_id' => 1,
            'vendor_id' => 1,
            'name' => 'Searchable Computer',
            'description' => 'Test Description',
            'serial_number' => 'SN-SEARCH-001',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
            'po_number' => 'PO-SEARCH-001',
            'status' => 'Available',
            'movement' => 'New Arrival'
        ]);

        $this->actingAs($this->admin)
            ->get('/assets?search=Searchable')
            ->assertStatus(200)
            ->assertSee('Searchable Computer');
    }
}
