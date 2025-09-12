<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;

class AssignRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        
        // Check if user has permission to assign roles
        if (!$user->hasAnyPermission(['manage_users', 'edit_users', 'manage_roles', 'admin_access'])) {
            return false;
        }
        
        // Prevent users from assigning roles higher than their own
        if ($this->has('role_id')) {
            $targetRole = Role::find($this->role_id);
            if ($targetRole && !$this->canAssignRole($user, $targetRole)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = Role::find($value);
                    if (!$role) {
                        $fail('The selected role does not exist.');
                        return;
                    }
                    
                    // Additional security checks
                    if (!$this->canAssignRole(Auth::user(), $role)) {
                        $fail('You do not have permission to assign this role.');
                    }
                }
            ]
        ];
    }
    
    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'role_id.required' => 'Please select a role to assign.',
            'role_id.exists' => 'The selected role does not exist.',
            'role_id.integer' => 'Invalid role selection.'
        ];
    }
    
    /**
     * Check if user can assign a specific role
     */
    private function canAssignRole(User $user, Role $role): bool
    {
        // Super Admin can assign any role
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        
        // Admin can assign most roles except Super Admin
        if ($user->hasRole('Admin')) {
            return !in_array($role->name, ['Super Admin']);
        }
        
        // Manager can only assign basic roles
        if ($user->hasRole('Manager')) {
            return in_array($role->name, ['User', 'IT Support']);
        }
        
        // Other roles cannot assign roles
        return false;
    }
    
    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        abort(403, 'You do not have permission to assign roles.');
    }
}