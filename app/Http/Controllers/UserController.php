<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('department');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $users = $query->orderBy('first_name')->paginate(15);
        $departments = Department::orderBy('name')->get();
        
        return view('users.index', compact('users', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('users.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'nullable|string|max:255|unique:users,employee_id',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        
        return redirect()->route('users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('department', 'assignedAssets.category');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments = Department::orderBy('name')->get();
        return view('users.edit', compact('user', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'employee_id' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $user->update($validated);
        
        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if user has assigned assets
        if ($user->assignedAssets()->count() > 0) {
            return redirect()->route('users.index')
                            ->with('error', 'Cannot delete user with assigned assets. Please reassign assets first.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully.');
    }
}
