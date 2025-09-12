# Role-Based Access Control (RBAC) Implementation

## Overview
This document describes the implementation of Role-Based Access Control (RBAC) in the Inventory Management System using Laravel Spatie Permission package.

## Architecture

### Database Schema
The RBAC system uses the following tables:
- `roles` - Stores role definitions
- `permissions` - Stores permission definitions  
- `role_permissions` - Links roles to permissions (configured as role_has_permissions)
- `model_has_permissions` - Direct user permissions
- `model_has_roles` - User role assignments

### Models
- **Role Model** (`app/Models/Role.php`) - Extends Spatie Role model
- **Permission Model** (`app/Models/Permission.php`) - Extends Spatie Permission model
- **User Model** (`app/Models/User.php`) - Uses HasRoles trait

## Configuration

### Spatie Permission Config
The system is configured in `config/permission.php` with:
- Custom table names mapping to existing schema
- Role and Permission model references
- Cache configuration

### Key Configuration Settings
```php
'table_names' => [
    'roles' => 'roles',
    'permissions' => 'permissions',
    'role_has_permissions' => 'role_permissions',
    'model_has_permissions' => 'model_has_permissions',
    'model_has_roles' => 'model_has_roles',
],
```

## Implementation Details

### Migration Integration
The existing custom RBAC tables were integrated with Spatie Permission by:
1. Creating Spatie permission tables migration
2. Mapping existing `role_permissions` table as `role_has_permissions`
3. Maintaining backward compatibility with existing data

### Permission Management
The system provides:
- Role creation and management
- Permission assignment to roles
- User role assignment
- Permission checking in controllers and views

### Controllers
- **RoleController** - Manages role CRUD operations
- **PermissionController** - Manages permission CRUD operations
- **UserController** - Handles user role assignments

### Middleware
Permission checking is implemented through:
- Route-level permission middleware
- Controller-level authorization
- View-level permission checks

## Usage Examples

### Checking Permissions in Controllers
```php
// Check if user has permission
if (auth()->user()->can('manage_assets')) {
    // Allow action
}

// Check if user has role
if (auth()->user()->hasRole('admin')) {
    // Allow action
}
```

### Blade Template Permission Checks
```php
@can('edit_assets')
    <button>Edit Asset</button>
@endcan

@role('admin')
    <a href="/admin">Admin Panel</a>
@endrole
```

### Route Protection
```php
Route::middleware(['permission:manage_users'])->group(function () {
    Route::resource('users', UserController::class);
});
```

## Default Roles and Permissions

### Roles
- **Admin** - Full system access
- **Super Admin** - System administration
- **Manager** - Department management
- **IT Support** - Technical support
- **User** - Basic user access

### Permission Categories
- **Asset Management** - Create, read, update, delete assets
- **User Management** - Manage user accounts
- **Department Management** - Manage departments
- **System Administration** - System configuration
- **Reporting** - Generate and view reports

## Security Features

### Permission Caching
- Automatic permission caching for performance
- Cache invalidation on permission changes
- Configurable cache drivers

### Authorization Policies
- Laravel policy integration
- Resource-based authorization
- Fine-grained access control

## Maintenance

### Adding New Permissions
1. Create permission via seeder or admin interface
2. Assign to appropriate roles
3. Implement permission checks in code
4. Clear permission cache

### Role Management
1. Create roles through admin interface
2. Assign permissions to roles
3. Assign roles to users
4. Test permission inheritance

## Troubleshooting

### Common Issues
- **Permission not working**: Clear cache with `php artisan permission:cache-reset`
- **Role assignment fails**: Check user model HasRoles trait
- **Database errors**: Verify table names in config match database

### Cache Management
```bash
# Clear permission cache
php artisan permission:cache-reset

# Clear application cache
php artisan cache:clear
php artisan config:clear
```

## Integration Points

### Asset Management
- Asset creation/editing permissions
- Department-based access control
- Assignment workflow permissions

### User Interface
- Dynamic menu generation based on permissions
- Conditional form elements
- Role-based dashboard content

### API Security
- Token-based authentication
- Permission-based API endpoints
- Rate limiting by role

## Performance Considerations

### Optimization Strategies
- Permission caching enabled
- Eager loading of roles and permissions
- Database indexing on foreign keys
- Minimal permission checks in loops

### Monitoring
- Track permission check frequency
- Monitor cache hit rates
- Log authorization failures

## Future Enhancements

### Planned Features
- Dynamic permission creation
- Role hierarchy implementation
- Time-based permissions
- Audit trail for permission changes

### Scalability
- Multi-tenant support preparation
- Permission inheritance optimization
- Advanced caching strategies

This RBAC implementation provides a robust, scalable foundation for access control in the inventory management system while maintaining compatibility with existing data structures.