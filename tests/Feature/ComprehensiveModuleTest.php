<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\Role;
use App\Models\Permission;
use App\Services\CacheService;
use App\Services\ErrorHandlingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission as SpatiePermission;

class ComprehensiveModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        SpatiePermission::create(['name' => 'view_assets']);
        SpatiePermission::create(['name' => 'create_assets']);
        SpatiePermission::create(['name' => 'edit_assets']);
        SpatiePermission::create(['name' => 'delete_assets']);
        SpatiePermission::create(['name' => 'view_users']);
        SpatiePermission::create(['name' => 'manage_users']);
        
        // Create roles
        $adminRole = SpatieRole::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view_assets', 'create_assets', 'edit_assets', 'delete_assets', 'view_users', 'manage_users']);
        
        $userRole = SpatieRole::create(['name' => 'user']);
        $userRole->givePermissionTo(['view_assets', 'view_users']);
        
        // Create test department
        $this->department = Department::create([
            'name' => 'IT Department',
            'description' => 'Information Technology'
        ]);
        
        // Create test vendor
        $this->vendor = Vendor::create([
            'name' => 'Test Vendor',
            'contact_person' => 'John Doe',
            'email' => 'vendor@test.com',
            'phone' => '123-456-7890'
        ]);
        
        // Create test category
        $this->category = AssetCategory::create([
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
            'department_id' => $this->department->id,
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
            'department_id' => $this->department->id,
            'status' => 1
        ]);
        $this->user->assignRole('user');
    }

    public function test_user_model_validation()
    {
        // Test user creation validation
        $userData = [
            'employee_id' => 'EMP003',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'department_id' => $this->department->id,
            'status' => 1
        ];
        
        $this->assertTrue(User::validationRules()['employee_id'] === 'required|string|max:50|unique:users,employee_id');
        $this->assertTrue(User::validationRules()['email'] === 'required|email|max:150|unique:users,email');
    }

    public function test_asset_model_validation()
    {
        // Test asset creation validation
        $assetData = [
            'asset_tag' => 'COMP-001',
            'category_id' => $this->category->id,
            'vendor_id' => $this->vendor->id,
            'name' => 'Test Computer',
            'serial_number' => 'SN123456',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
            'po_number' => 'PO-001',
            'status' => 'Active',
            'movement' => 'New Arrival'
        ];
        
        $rules = Asset::validationRules();
        $this->assertArrayHasKey('asset_tag', $rules);
        $this->assertArrayHasKey('category_id', $rules);
        $this->assertArrayHasKey('vendor_id', $rules);
        $this->assertStringContains('Active', $rules['status']);
        $this->assertStringContains('New Arrival', $rules['movement']);
    }

    public function test_cache_service_functionality()
    {
        $cacheService = new CacheService();
        
        // Test dashboard stats caching
        $stats = [
            'total_assets' => 10,
            'assigned_assets' => 5,
            'available_assets' => 5
        ];
        
        $cacheService->cacheDashboardStats($stats);
        $cachedStats = $cacheService->getDashboardStats();
        
        $this->assertEquals($stats, $cachedStats);
    }

    public function test_error_handling_service()
    {
        $errorService = new ErrorHandlingService();
        
        // Test field error handling
        $errorService->addFieldError('name', 'Name is required', 'validation');
        $errors = $errorService->getFieldErrors('name');
        
        $this->assertCount(1, $errors);
        $this->assertEquals('Name is required', $errors[0]['message']);
        
        // Test system error handling
        $errorService->addSystemError('Database connection failed', 'system');
        $systemErrors = $errorService->getSystemErrors();
        
        $this->assertCount(1, $systemErrors);
        $this->assertEquals('Database connection failed', $systemErrors[0]['message']);
    }

    public function test_asset_crud_operations()
    {
        $this->actingAs($this->admin);
        
        // Test asset creation
        $assetData = [
            'asset_tag' => 'COMP-001',
            'category_id' => $this->category->id,
            'vendor_id' => $this->vendor->id,
            'name' => 'Test Computer',
            'serial_number' => 'SN123456',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
            'po_number' => 'PO-001',
            'status' => 'Active',
            'movement' => 'New Arrival'
        ];
        
        $response = $this->post('/assets', $assetData);
        $response->assertRedirect('/assets');
        
        // Test asset retrieval
        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'COMP-001',
            'name' => 'Test Computer'
        ]);
        
        // Test asset update
        $asset = Asset::where('asset_tag', 'COMP-001')->first();
        $response = $this->put("/assets/{$asset->id}", array_merge($assetData, [
            'name' => 'Updated Computer'
        ]));
        $response->assertRedirect('/assets');
        
        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'COMP-001',
            'name' => 'Updated Computer'
        ]);
    }

    public function test_permission_system()
    {
        // Test admin permissions
        $this->assertTrue($this->admin->hasPermissionTo('view_assets'));
        $this->assertTrue($this->admin->hasPermissionTo('create_assets'));
        $this->assertTrue($this->admin->hasPermissionTo('edit_assets'));
        
        // Test user permissions
        $this->assertTrue($this->user->hasPermissionTo('view_assets'));
        $this->assertFalse($this->user->hasPermissionTo('create_assets'));
        $this->assertFalse($this->user->hasPermissionTo('edit_assets'));
    }

    public function test_asset_assignment_workflow()
    {
        $this->actingAs($this->admin);
        
        // Create an asset
        $asset = Asset::create([
            'asset_tag' => 'COMP-002',
            'category_id' => $this->category->id,
            'vendor_id' => $this->vendor->id,
            'name' => 'Assignment Test Computer',
            'serial_number' => 'SN123457',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
            'po_number' => 'PO-002',
            'status' => 'Active',
            'movement' => 'New Arrival'
        ]);
        
        // Test asset assignment
        $assignmentData = [
            'assigned_to' => $this->user->id,
            'assigned_date' => '2024-01-15',
            'notes' => 'Test assignment'
        ];
        
        $response = $this->post("/assets/{$asset->id}/assign", $assignmentData);
        $response->assertRedirect();
        
        // Verify assignment
        $asset->refresh();
        $this->assertEquals($this->user->id, $asset->assigned_to);
        $this->assertEquals('Pending Confirmation', $asset->status);
    }

    public function test_database_relationships()
    {
        // Test user-department relationship
        $this->assertEquals($this->department->id, $this->user->department_id);
        $this->assertEquals('IT Department', $this->user->department->name);
        
        // Test asset-category relationship
        $asset = Asset::create([
            'asset_tag' => 'COMP-003',
            'category_id' => $this->category->id,
            'vendor_id' => $this->vendor->id,
            'name' => 'Relationship Test Computer',
            'serial_number' => 'SN123458',
            'purchase_date' => '2024-01-01',
            'cost' => 1000.00,
            'po_number' => 'PO-003',
            'status' => 'Active',
            'movement' => 'New Arrival'
        ]);
        
        $this->assertEquals($this->category->id, $asset->category_id);
        $this->assertEquals('Computers', $asset->category->name);
    }

    public function test_middleware_security()
    {
        // Test unauthorized access
        $response = $this->get('/assets');
        $response->assertRedirect('/login');
        
        // Test authorized access
        $this->actingAs($this->user);
        $response = $this->get('/assets');
        $response->assertStatus(200);
    }

    public function test_api_endpoints()
    {
        $this->actingAs($this->admin);
        
        // Test API asset listing
        $response = $this->getJson('/api/assets');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
        
        // Test API asset statistics
        $response = $this->getJson('/api/assets/statistics/overview');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_assets',
            'assigned_assets',
            'available_assets',
            'assets_by_status',
            'assets_by_category',
            'total_value'
        ]);
    }
}
