<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ValidationService
{
    /**
     * Validate import data with comprehensive rules
     */
    public function validateImportData(string $module, array $data, ?int $excludeId = null): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'field_errors' => []
        ];

        try {
            $rules = $this->getValidationRules($module, $excludeId);
            $messages = $this->getValidationMessages($module);
            $attributes = $this->getFieldAttributes($module);

            $validator = Validator::make($data, $rules, $messages, $attributes);

            if ($validator->fails()) {
                $result['valid'] = false;
                $result['errors'] = $validator->errors()->all();
                $result['field_errors'] = $validator->errors()->toArray();
            }

            // Additional custom validations
            $customValidation = $this->performCustomValidations($module, $data, $excludeId);
            if (!$customValidation['valid']) {
                $result['valid'] = false;
                $result['errors'] = array_merge($result['errors'], $customValidation['errors']);
                $result['field_errors'] = array_merge($result['field_errors'], $customValidation['field_errors']);
            }

            $result['warnings'] = array_merge($result['warnings'], $customValidation['warnings']);

        } catch (\Exception $e) {
            $result['valid'] = false;
            $result['errors'][] = 'Validation error: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Get validation rules for each module
     */
    private function getValidationRules(string $module, ?int $excludeId = null): array
    {
        switch ($module) {
            case 'assets':
                return [
                    'asset_tag' => [
                        'required',
                        'string',
                        'max:50',
                        'regex:/^[A-Z0-9\-_]+$/',
                        Rule::unique('assets', 'asset_tag')->ignore($excludeId)
                    ],
                    'category_name' => 'required|string|max:255',
                    'vendor_name' => 'required|string|max:255',
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'serial_number' => [
                        'nullable',
                        'string',
                        'max:100',
                        'regex:/^[A-Z0-9\-_]+$/i',
                        Rule::unique('assets', 'serial_number')->ignore($excludeId)
                    ],
                    'purchase_date' => 'nullable|date|before_or_equal:today',
                    'warranty_end' => 'nullable|date|after_or_equal:purchase_date',
                    'cost' => 'nullable|numeric|min:0|max:999999999.99',
                    'status' => 'nullable|in:Available,Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Disposed'
                ];

            case 'computers':
                $baseRules = $this->getValidationRules('assets', $excludeId);
                return array_merge($baseRules, [
                    'processor' => 'required|string|max:255',
                    'ram' => ['required', 'string', 'max:255', 'regex:/^\d+\s*(GB|MB|TB)\s*(DDR[3-5]?)?$/i'],
                    'storage' => ['required', 'string', 'max:255', 'regex:/^\d+\s*(GB|TB)\s*(SSD|HDD|NVME)?$/i'],
                    'os' => 'required|string|max:255'
                ]);

            case 'users':
                return [
                    'employee_no' => [
                        'required',
                        'string',
                        'max:50',
                        'regex:/^[A-Z0-9\-_]+$/i',
                        Rule::unique('users', 'employee_no')->ignore($excludeId)
                    ],
                    'employee_id' => [
                        'nullable',
                        'string',
                        'max:50',
                        Rule::unique('users', 'employee_id')->ignore($excludeId)
                    ],
                    'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
                    'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
                    'email' => [
                        'required',
                        'email:rfc,dns',
                        'max:255',
                        Rule::unique('users', 'email')->ignore($excludeId)
                    ],
                    'department_name' => 'required|string|max:255',
                    'position' => 'nullable|string|max:255',
                    'role_name' => 'nullable|string|max:255',
                    'status' => 'nullable|in:active,inactive'
                ];

            case 'departments':
                return [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('departments', 'name')->ignore($excludeId)
                    ],
                    'description' => 'nullable|string|max:1000',
                    'manager_email' => 'nullable|email:rfc,dns'
                ];

            case 'vendors':
                return [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('vendors', 'name')->ignore($excludeId)
                    ],
                    'contact_person' => 'nullable|string|max:255',
                    'email' => 'nullable|email:rfc,dns|max:255',
                    'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
                    'address' => 'nullable|string|max:500'
                ];

            default:
                return [];
        }
    }

    /**
     * Get custom validation messages
     */
    private function getValidationMessages(string $module): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute has already been taken.',
            'email' => 'The :attribute must be a valid email address.',
            'date' => 'The :attribute must be a valid date.',
            'numeric' => 'The :attribute must be a number.',
            'min' => 'The :attribute must be at least :min.',
            'max' => 'The :attribute may not be greater than :max.',
            'regex' => 'The :attribute format is invalid.',
            'in' => 'The selected :attribute is invalid.',
            'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
            'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
            'asset_tag.regex' => 'Asset tag must contain only letters, numbers, hyphens, and underscores.',
            'serial_number.regex' => 'Serial number must contain only letters, numbers, hyphens, and underscores.',
            'employee_no.regex' => 'Employee number must contain only letters, numbers, hyphens, and underscores.',
            'first_name.regex' => 'First name must contain only letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name must contain only letters, spaces, hyphens, and apostrophes.',
            'ram.regex' => 'RAM format should be like "16GB DDR4" or "8GB".',
            'storage.regex' => 'Storage format should be like "512GB SSD" or "1TB HDD".',
            'phone.regex' => 'Phone number format is invalid.'
        ];
    }

    /**
     * Get field attributes for better error messages
     */
    private function getFieldAttributes(string $module): array
    {
        $common = [
            'asset_tag' => 'asset tag',
            'category_name' => 'category name',
            'vendor_name' => 'vendor name',
            'serial_number' => 'serial number',
            'purchase_date' => 'purchase date',
            'warranty_end' => 'warranty end date',
            'employee_no' => 'employee number',
            'employee_id' => 'employee ID',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'department_name' => 'department name',
            'role_name' => 'role name',
            'contact_person' => 'contact person',
            'manager_email' => 'manager email'
        ];

        return $common;
    }

    /**
     * Perform custom validations beyond basic rules
     */
    private function performCustomValidations(string $module, array $data, ?int $excludeId = null): array
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'field_errors' => []
        ];

        switch ($module) {
            case 'assets':
            case 'computers':
                $this->validateAssetRelationships($data, $result);
                $this->validateAssetDates($data, $result);
                $this->validateAssetFinancials($data, $result);
                break;

            case 'users':
                $this->validateUserRelationships($data, $result);
                $this->validateUserData($data, $result);
                break;

            case 'departments':
                $this->validateDepartmentRelationships($data, $result);
                break;

            case 'vendors':
                $this->validateVendorData($data, $result);
                break;
        }

        return $result;
    }

    /**
     * Validate asset relationships (category, vendor)
     */
    private function validateAssetRelationships(array $data, array &$result): void
    {
        // Validate category exists
        if (!empty($data['category_name'])) {
            $category = AssetCategory::where('name', $data['category_name'])->first();
            if (!$category) {
                $result['valid'] = false;
                $result['errors'][] = "Category '{$data['category_name']}' does not exist.";
                $result['field_errors']['category_name'][] = "Category '{$data['category_name']}' does not exist.";
            }
        }

        // Validate vendor exists
        if (!empty($data['vendor_name'])) {
            $vendor = Vendor::where('name', $data['vendor_name'])->first();
            if (!$vendor) {
                $result['valid'] = false;
                $result['errors'][] = "Vendor '{$data['vendor_name']}' does not exist.";
                $result['field_errors']['vendor_name'][] = "Vendor '{$data['vendor_name']}' does not exist.";
            }
        }
    }

    /**
     * Validate asset dates
     */
    private function validateAssetDates(array $data, array &$result): void
    {
        if (!empty($data['purchase_date']) && !empty($data['warranty_end'])) {
            try {
                $purchaseDate = Carbon::parse($data['purchase_date']);
                $warrantyEnd = Carbon::parse($data['warranty_end']);

                if ($warrantyEnd->lt($purchaseDate)) {
                    $result['valid'] = false;
                    $result['errors'][] = 'Warranty end date cannot be before purchase date.';
                    $result['field_errors']['warranty_end'][] = 'Warranty end date cannot be before purchase date.';
                }

                // Warning for very long warranty periods
                if ($warrantyEnd->diffInYears($purchaseDate) > 10) {
                    $result['warnings'][] = 'Warranty period is unusually long (over 10 years).';
                }
            } catch (\Exception $e) {
                $result['valid'] = false;
                $result['errors'][] = 'Invalid date format.';
            }
        }

        // Warning for future purchase dates
        if (!empty($data['purchase_date'])) {
            try {
                $purchaseDate = Carbon::parse($data['purchase_date']);
                if ($purchaseDate->gt(Carbon::today())) {
                    $result['warnings'][] = 'Purchase date is in the future.';
                }
            } catch (\Exception $e) {
                // Date validation will be caught by main validator
            }
        }
    }

    /**
     * Validate asset financial data
     */
    private function validateAssetFinancials(array $data, array &$result): void
    {
        if (!empty($data['cost'])) {
            $cost = floatval($data['cost']);
            
            // Warning for unusually high costs
            if ($cost > 1000000) {
                $result['warnings'][] = 'Asset cost is unusually high (over 1,000,000).';
            }
            
            // Warning for zero cost
            if ($cost == 0) {
                $result['warnings'][] = 'Asset cost is zero.';
            }
        }
    }

    /**
     * Validate user relationships (department, role)
     */
    private function validateUserRelationships(array $data, array &$result): void
    {
        // Validate department exists
        if (!empty($data['department_name'])) {
            $department = Department::where('name', $data['department_name'])->first();
            if (!$department) {
                $result['valid'] = false;
                $result['errors'][] = "Department '{$data['department_name']}' does not exist.";
                $result['field_errors']['department_name'][] = "Department '{$data['department_name']}' does not exist.";
            }
        }

        // Validate role exists (if provided)
        if (!empty($data['role_name'])) {
            $role = \App\Models\Role::where('name', $data['role_name'])->first();
            if (!$role) {
                $result['valid'] = false;
                $result['errors'][] = "Role '{$data['role_name']}' does not exist.";
                $result['field_errors']['role_name'][] = "Role '{$data['role_name']}' does not exist.";
            }
        }
    }

    /**
     * Validate user-specific data
     */
    private function validateUserData(array $data, array &$result): void
    {
        // Check email domain if company domain is configured
        if (!empty($data['email'])) {
            $emailDomain = substr(strrchr($data['email'], '@'), 1);
            // This could be configurable
            $allowedDomains = ['company.com', 'organization.org']; // Example domains
            
            // This is just a warning, not an error
            if (!empty($allowedDomains) && !in_array($emailDomain, $allowedDomains)) {
                $result['warnings'][] = "Email domain '{$emailDomain}' is not in the standard company domains.";
            }
        }
    }

    /**
     * Validate department relationships
     */
    private function validateDepartmentRelationships(array $data, array &$result): void
    {
        // Validate manager exists (if provided)
        if (!empty($data['manager_email'])) {
            $manager = User::where('email', $data['manager_email'])->first();
            if (!$manager) {
                $result['valid'] = false;
                $result['errors'][] = "Manager with email '{$data['manager_email']}' does not exist.";
                $result['field_errors']['manager_email'][] = "Manager with email '{$data['manager_email']}' does not exist.";
            }
        }
    }

    /**
     * Validate vendor data
     */
    private function validateVendorData(array $data, array &$result): void
    {
        // Additional vendor-specific validations can be added here
        // For example, checking if contact person email matches vendor email domain
    }

    /**
     * Validate serial number uniqueness across all asset types
     */
    public function validateSerialNumberUniqueness(string $serialNumber, ?int $excludeAssetId = null): bool
    {
        if (empty($serialNumber)) {
            return true; // Serial number is optional
        }

        $query = Asset::where('serial_number', $serialNumber);
        
        if ($excludeAssetId) {
            $query->where('id', '!=', $excludeAssetId);
        }

        return !$query->exists();
    }

    /**
     * Validate asset tag uniqueness
     */
    public function validateAssetTagUniqueness(string $assetTag, ?int $excludeAssetId = null): bool
    {
        $query = Asset::where('asset_tag', $assetTag);
        
        if ($excludeAssetId) {
            $query->where('id', '!=', $excludeAssetId);
        }

        return !$query->exists();
    }

    /**
     * Get real-time validation rules for frontend
     */
    public function getFrontendValidationRules(string $module): array
    {
        $rules = $this->getValidationRules($module);
        $frontendRules = [];

        foreach ($rules as $field => $fieldRules) {
            $frontendRules[$field] = [
                'required' => in_array('required', $fieldRules),
                'type' => $this->getFieldType($field, $fieldRules),
                'maxLength' => $this->extractMaxLength($fieldRules),
                'pattern' => $this->extractPattern($fieldRules),
                'options' => $this->extractOptions($fieldRules)
            ];
        }

        return $frontendRules;
    }

    /**
     * Extract field type for frontend validation
     */
    private function getFieldType(string $field, array $rules): string
    {
        if (in_array('email', $rules) || str_contains($field, 'email')) {
            return 'email';
        }
        if (in_array('date', $rules) || str_contains($field, 'date')) {
            return 'date';
        }
        if (in_array('numeric', $rules) || str_contains($field, 'cost')) {
            return 'number';
        }
        return 'text';
    }

    /**
     * Extract max length from validation rules
     */
    private function extractMaxLength(array $rules): ?int
    {
        foreach ($rules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'max:')) {
                return (int) substr($rule, 4);
            }
        }
        return null;
    }

    /**
     * Extract regex pattern from validation rules
     */
    private function extractPattern(array $rules): ?string
    {
        foreach ($rules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'regex:')) {
                return substr($rule, 6);
            }
        }
        return null;
    }

    /**
     * Extract options from validation rules
     */
    private function extractOptions(array $rules): ?array
    {
        foreach ($rules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'in:')) {
                return explode(',', substr($rule, 3));
            }
        }
        return null;
    }
}