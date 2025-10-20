<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\User;
use App\Models\AssetCategory;
use App\Models\AssetAssignmentConfirmation;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\Maintenance;
use App\Models\Disposal;
use App\Models\Computer;
use App\Models\Monitor;
use App\Models\Printer;
use App\Models\Transfer;

class GlobalSearchController extends Controller
{
    /**
     * Perform global search across all modules
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        // Debug logging (can be removed in production)
        // \Log::info('Global search request', ['query' => $query, 'user_id' => auth()->id()]);
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter at least 2 characters to search',
                'results' => []
            ]);
        }

        $searchTerm = '%' . $query . '%';
        $results = [];

        try {
            // Search Assets
            $assets = Asset::where(function($q) use ($searchTerm) {
                $q->where('asset_tag', 'like', $searchTerm)
                  ->orWhere('name', 'like', $searchTerm)
                  ->orWhere('serial_number', 'like', $searchTerm)
                  ->orWhere('model', 'like', $searchTerm);
            })
            ->with(['category', 'vendor', 'assignedUser', 'department'])
            ->limit(10)
            ->get();

            // Debug logging (can be removed in production)
            // \Log::info('Asset search results', ['search_term' => $searchTerm, 'assets_found' => $assets->count()]);

            foreach ($assets as $asset) {
                $results[] = [
                    'type' => 'asset',
                    'title' => $asset->asset_tag . ' - ' . $asset->name,
                    'description' => 'Asset • ' . ($asset->category->name ?? 'No Category') . ' • ' . ($asset->status ?? 'Unknown Status'),
                    'url' => route('assets.show', $asset->id),
                    'icon' => 'fas fa-laptop',
                    'category' => 'Assets'
                ];
            }

            // Search Users
            $users = User::where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('employee_id', 'like', $searchTerm);
            })
            ->limit(5)
            ->get();

            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'title' => $user->first_name . ' ' . $user->last_name,
                    'description' => 'User • ' . ($user->email ?? 'No Email') . ' • ' . ($user->department->name ?? 'No Department'),
                    'url' => route('users.show', $user->id),
                    'icon' => 'fas fa-user',
                    'category' => 'Users'
                ];
            }

            // Search Asset Categories
            $categories = AssetCategory::where('name', 'like', $searchTerm)
                ->limit(5)
                ->get();

            foreach ($categories as $category) {
                $results[] = [
                    'type' => 'category',
                    'title' => $category->name,
                    'description' => 'Category • ' . $category->description,
                    'url' => route('asset-categories.show', $category->id),
                    'icon' => 'fas fa-tags',
                    'category' => 'Categories'
                ];
            }

            // Search Departments
            $departments = Department::where('name', 'like', $searchTerm)
                ->limit(5)
                ->get();

            foreach ($departments as $department) {
                $results[] = [
                    'type' => 'department',
                    'title' => $department->name,
                    'description' => 'Department • ' . ($department->description ?? 'No Description'),
                    'url' => route('departments.show', $department->id),
                    'icon' => 'fas fa-building',
                    'category' => 'Departments'
                ];
            }

            // Search Vendors
            $vendors = Vendor::where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('contact_person', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            })
            ->limit(5)
            ->get();

            foreach ($vendors as $vendor) {
                $results[] = [
                    'type' => 'vendor',
                    'title' => $vendor->name,
                    'description' => 'Vendor • ' . ($vendor->contact_person ?? 'No Contact') . ' • ' . ($vendor->email ?? 'No Email'),
                    'url' => route('vendors.show', $vendor->id),
                    'icon' => 'fas fa-truck',
                    'category' => 'Vendors'
                ];
            }

            // Search Maintenance Records
            $maintenances = Maintenance::whereHas('asset', function($q) use ($searchTerm) {
                $q->where('asset_tag', 'like', $searchTerm)
                  ->orWhere('name', 'like', $searchTerm);
            })
            ->orWhere('issue_reported', 'like', $searchTerm)
            ->orWhere('repair_action', 'like', $searchTerm)
            ->with(['asset'])
            ->limit(5)
            ->get();

            foreach ($maintenances as $maintenance) {
                $title = $maintenance->asset->asset_tag . ' - ' . ($maintenance->issue_reported ?? 'Maintenance Record');
                $results[] = [
                    'type' => 'maintenance',
                    'title' => $title,
                    'description' => 'Maintenance • ' . ($maintenance->status ?? 'Unknown Status') . ' • ' . ($maintenance->start_date ? $maintenance->start_date->format('M d, Y') : 'No Date'),
                    'url' => route('maintenance.show', $maintenance->id),
                    'icon' => 'fas fa-wrench',
                    'category' => 'Maintenance'
                ];
            }

            // Search Disposals
            $disposals = Disposal::whereHas('asset', function($q) use ($searchTerm) {
                $q->where('asset_tag', 'like', $searchTerm)
                  ->orWhere('name', 'like', $searchTerm);
            })
            ->orWhere('remarks', 'like', $searchTerm)
            ->with(['asset'])
            ->limit(5)
            ->get();

            foreach ($disposals as $disposal) {
                $title = $disposal->asset->asset_tag . ' - ' . ($disposal->remarks ?? 'Disposal Record');
                $results[] = [
                    'type' => 'disposal',
                    'title' => $title,
                    'description' => 'Disposal • ' . ($disposal->disposal_type ?? 'Unknown Type') . ' • ' . ($disposal->disposal_date ? $disposal->disposal_date->format('M d, Y') : 'No Date'),
                    'url' => route('disposals.show', $disposal->id),
                    'icon' => 'fas fa-trash',
                    'category' => 'Disposals'
                ];
            }

            // Sort results by relevance (exact matches first, then partial matches)
            usort($results, function($a, $b) use ($query) {
                $aExact = strtolower($a['title']) === strtolower($query);
                $bExact = strtolower($b['title']) === strtolower($query);
                
                if ($aExact && !$bExact) return -1;
                if (!$aExact && $bExact) return 1;
                
                return 0;
            });

            // Limit total results
            $results = array_slice($results, 0, 20);

            return response()->json([
                'success' => true,
                'query' => $query,
                'results' => $results,
                'total' => count($results)
            ]);

        } catch (\Exception $e) {
            \Log::error('Global search error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
                'results' => []
            ]);
        }
    }

    /**
     * Show global search results page
     */
    public function results(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->back()->with('error', 'Please enter a search term.');
        }

        // Perform the same search but return a view
        $searchResponse = $this->search($request);
        $data = json_decode($searchResponse->getContent(), true);
        
        if (!$data['success']) {
            return redirect()->back()->with('error', $data['message']);
        }

        $results = collect($data['results']);
        $groupedResults = $results->groupBy('category');

        return view('global-search.results', compact('query', 'results', 'groupedResults'));
    }
}
