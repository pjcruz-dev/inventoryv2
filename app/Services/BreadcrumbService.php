<?php

namespace App\Services;

class BreadcrumbService
{
    protected $breadcrumbs = [];

    /**
     * Add a breadcrumb item
     */
    public function add(string $title, string $url = null, bool $active = false): self
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'url' => $url,
            'active' => $active,
            'icon' => null
        ];
        
        return $this;
    }

    /**
     * Add a breadcrumb item with icon
     */
    public function addWithIcon(string $title, string $icon, string $url = null, bool $active = false): self
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'url' => $url,
            'active' => $active,
            'icon' => $icon
        ];
        
        return $this;
    }

    /**
     * Add home breadcrumb (disabled - not usable)
     */
    public function addHome(): self
    {
        // Home route is not usable, so we skip it
        return $this;
    }

    /**
     * Add dashboard breadcrumb
     */
    public function addDashboard(): self
    {
        return $this->addWithIcon('Home', 'fas fa-home', route('dashboard'));
    }

    /**
     * Add assets breadcrumbs
     */
    public function addAssets(): self
    {
        return $this->addWithIcon('Assets', 'fas fa-laptop', route('assets.index'));
    }

    /**
     * Add asset categories breadcrumbs
     */
    public function addAssetCategories(): self
    {
        return $this->addWithIcon('Asset Categories', 'fas fa-tags', route('asset-categories.index'));
    }

    /**
     * Add users breadcrumbs
     */
    public function addUsers(): self
    {
        return $this->addWithIcon('Users', 'fas fa-users', route('users.index'));
    }

    /**
     * Add departments breadcrumbs
     */
    public function addDepartments(): self
    {
        return $this->addWithIcon('Departments', 'fas fa-building', route('departments.index'));
    }

    /**
     * Add roles breadcrumbs
     */
    public function addRoles(): self
    {
        return $this->addWithIcon('Roles', 'fas fa-user-shield', route('roles.index'));
    }

    /**
     * Add maintenance breadcrumbs
     */
    public function addMaintenance(): self
    {
        return $this->addWithIcon('Maintenance', 'fas fa-tools', route('maintenance.index'));
    }

    /**
     * Add reports breadcrumbs
     */
    public function addReports(): self
    {
        return $this->addWithIcon('Reports', 'fas fa-chart-bar', route('assets.print-employee-assets'));
    }

    /**
     * Add accountability forms breadcrumbs
     */
    public function addAccountability(): self
    {
        return $this->addWithIcon('Accountability Forms', 'fas fa-file-alt', route('accountability.index'));
    }

    /**
     * Add import/export breadcrumbs
     */
    public function addImportExport(): self
    {
        return $this->addWithIcon('Import/Export', 'fas fa-file-import', route('import-export.interface'));
    }

    /**
     * Get all breadcrumbs
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Clear all breadcrumbs
     */
    public function clear(): self
    {
        $this->breadcrumbs = [];
        return $this;
    }

    /**
     * Set breadcrumbs for a specific route
     */
    public function setForRoute(string $routeName, array $parameters = []): self
    {
        $this->clear();

        switch ($routeName) {
            case 'home':
                $this->addDashboard()->add('Overview', null, true);
                break;

            case 'dashboard':
                $this->addDashboard()->add('Overview', null, true);
                break;

            case 'assets.index':
                $this->addDashboard()->addAssets()->add('List', null, true);
                break;

            case 'assets.create':
                $this->addDashboard()->addAssets()->add('Create New Asset', null, true);
                break;

            case 'assets.show':
                $this->addDashboard()->addAssets()->add('Asset Details', null, true);
                break;

            case 'assets.edit':
                $this->addDashboard()->addAssets()->add('Edit Asset', null, true);
                break;

            case 'asset-categories.index':
                $this->addDashboard()->addAssetCategories()->add('List', null, true);
                break;

            case 'asset-categories.create':
                $this->addDashboard()->addAssetCategories()->add('Create Category', null, true);
                break;

            case 'asset-categories.edit':
                $this->addDashboard()->addAssetCategories()->add('Edit Category', null, true);
                break;

            case 'users.index':
                $this->addDashboard()->addUsers()->add('List', null, true);
                break;

            case 'users.create':
                $this->addDashboard()->addUsers()->add('Create User', null, true);
                break;

            case 'users.show':
                $this->addDashboard()->addUsers()->add('User Details', null, true);
                break;

            case 'users.edit':
                $this->addDashboard()->addUsers()->add('Edit User', null, true);
                break;

            case 'departments.index':
                $this->addDashboard()->addDepartments()->add('List', null, true);
                break;

            case 'departments.create':
                $this->addDashboard()->addDepartments()->add('Create Department', null, true);
                break;

            case 'departments.edit':
                $this->addDashboard()->addDepartments()->add('Edit Department', null, true);
                break;

            case 'roles.index':
                $this->addDashboard()->addRoles()->add('List', null, true);
                break;

            case 'roles.create':
                $this->addDashboard()->addRoles()->add('Create Role', null, true);
                break;

            case 'roles.edit':
                $this->addDashboard()->addRoles()->add('Edit Role', null, true);
                break;

            case 'maintenance.index':
                $this->addDashboard()->addMaintenance()->add('List', null, true);
                break;

            case 'accountability.index':
                $this->addDashboard()->addAccountability()->add('List', null, true);
                break;

            case 'accountability.form':
                $this->addDashboard()->addAccountability()->add('Generate Form', null, true);
                break;

            case 'import-export.interface':
                $this->addDashboard()->addImportExport()->add('Interface', null, true);
                break;

            case 'asset-assignments.index':
                $this->addDashboard()->addWithIcon('Asset Assignments', 'fas fa-user-check', route('asset-assignments.index'))->add('List', null, true);
                break;

            case 'asset-assignments.create':
                $this->addDashboard()->addWithIcon('Asset Assignments', 'fas fa-user-check', route('asset-assignments.index'))->add('Create Assignment', null, true);
                break;

            case 'vendors.index':
                $this->addDashboard()->addWithIcon('Vendors', 'fas fa-store', route('vendors.index'))->add('List', null, true);
                break;

            case 'vendors.create':
                $this->addDashboard()->addWithIcon('Vendors', 'fas fa-store', route('vendors.index'))->add('Create Vendor', null, true);
                break;

            case 'vendors.show':
                $this->addDashboard()->addWithIcon('Vendors', 'fas fa-store', route('vendors.index'))->add('Vendor Details', null, true);
                break;

            case 'vendors.edit':
                $this->addDashboard()->addWithIcon('Vendors', 'fas fa-store', route('vendors.index'))->add('Edit Vendor', null, true);
                break;

            case 'logs.index':
                $this->addDashboard()->addWithIcon('Activity Logs', 'fas fa-history', route('logs.index'))->add('List', null, true);
                break;

            case 'logs.show':
                $this->addDashboard()->addWithIcon('Activity Logs', 'fas fa-history', route('logs.index'))->add('Log Details', null, true);
                break;

            case 'permissions.index':
                $this->addDashboard()->addWithIcon('Permissions', 'fas fa-key', route('permissions.index'))->add('List', null, true);
                break;

            default:
                // Default breadcrumb for unknown routes
                $this->addDashboard()->add('Current Page', null, true);
                break;
        }

        return $this;
    }
}
