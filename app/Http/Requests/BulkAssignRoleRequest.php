<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;

class BulkAssignRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        
        // Check if user has permission to bulk assign roles
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
        
        // Check if user can modify all target users
        if ($this->has('user_ids')) {
            $targetUsers = User::whereIn('id', $this->user_ids)->get();
            foreach ($targetUsers as $targetUser) {
                if (!$this->canModifyUser($user, $targetUser)) {
                    return false;
                }
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
            'user_ids' => [
                'required',
                'array',
                'min:1',
                'max:50', // Limit bulk operations
                function ($attribute, $value, $fail) {
                    if (count($value) > 50) {
                        $fail('You can only assign roles to a maximum of 50 users at once.');
                    }
                }
            ],
            'user_ids.*' => [
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && !$this->canModifyUser(Auth::user(), $user)) {
                        $fail("You do not have permission to modify user: {$user->name}");
                    }
                }
            ],
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
            'user_ids.required' => 'Please select at least one user.',
            'user_ids.min' => 'Please select at least one user.',
            'user_ids.max' => 'You can select a maximum of 50 users at once.',
            'user_ids.*.exists' => 'One or more selected users do not exist.',
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
     * Check if user can modify another user
     */
    private function canModifyUser(User $currentUser, User $targetUser): bool
    {
        // Users cannot modify themselves through bulk operations
        if ($currentUser->id === $targetUser->id) {
            return false;
        }
        
        // Super Admin can modify anyone except other Super Admins
        if ($currentUser->hasRole('Super Admin')) {
            return !$targetUser->hasRole('Super Admin') || $currentUser->id === $targetUser->id;
        }
        
        // Admin can modify users with lower roles
        if ($currentUser->hasRole('Admin')) {
            return !$targetUser->hasAnyRole(['Super Admin', 'Admin']);
        }
        
        // Manager can only modify basic users
        if ($currentUser->hasRole('Manager')) {
            return $targetUser->hasRole('User');
        }
        
        // Other roles cannot modify users
        return false;
    }
    
    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        abort(403, 'You do not have permission to perform bulk role assignments.');
    }
}