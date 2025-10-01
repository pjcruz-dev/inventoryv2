<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_departments')->only(['index', 'show']);
        $this->middleware('permission:create_departments')->only(['create', 'store']);
        $this->middleware('permission:edit_departments')->only(['edit', 'update']);
        $this->middleware('permission:delete_departments')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Department::with(['manager', 'users', 'assets', 'parent', 'children']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('manager', function($managerQuery) use ($search) {
                      $managerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('parent', function($parentQuery) use ($search) {
                      $parentQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $departments = $query->paginate(10)->appends(request()->query());
        
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('status', 1)
                    ->orderBy('first_name')
                    ->get();
                    
        return view('departments.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'nullable|string|max:10|unique:departments',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        Department::create($validated);
        
        return redirect()->route('departments.index')
                        ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load(['manager', 'users.assignedAssets', 'assets.category', 'assets.assignedUser', 'parent', 'children']);
        
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $users = User::where('status', 'active')
                    ->orderBy('first_name')
                    ->get();
                    
        return view('departments.edit', compact('department', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)],
            'code' => ['nullable', 'string', 'max:10', Rule::unique('departments')->ignore($department->id)],
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        $department->update($validated);
        
        return redirect()->route('departments.show', $department)
                        ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        // Check if department has users or assets
        if ($department->users()->count() > 0) {
            return redirect()->route('departments.show', $department)
                            ->with('error', 'Cannot delete department with assigned users. Please reassign users first.');
        }
        
        if ($department->assets()->count() > 0) {
            return redirect()->route('departments.show', $department)
                            ->with('error', 'Cannot delete department with assigned assets. Please reassign assets first.');
        }
        
        $department->delete();
        
        return redirect()->route('departments.index')
                        ->with('success', 'Department deleted successfully.');
    }
}
