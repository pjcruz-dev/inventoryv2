<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Asset;

class PreventMaintenanceAssetEdit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if this is an asset edit/update request
        if ($request->routeIs('assets.edit') || $request->routeIs('assets.update')) {
            $asset = $request->route('asset');
            
            if ($asset && in_array($asset->status, ['Under Maintenance', 'Disposed'])) {
                $statusMessage = $asset->status === 'Under Maintenance' 
                    ? 'under maintenance' 
                    : 'disposed';
                
                return redirect()->back()
                    ->with('error', "Cannot edit asset that is currently {$statusMessage}. Please complete or cancel the maintenance first, or restore the asset from disposal.");
            }
        }
        
        // Check if this is an asset assignment request
        if ($request->routeIs('assets.assign') || $request->routeIs('asset-assignments.*')) {
            $asset = $request->route('asset');
            
            if (!$asset && $request->input('asset_id')) {
                $asset = Asset::find($request->input('asset_id'));
            }
            
            if ($asset && in_array($asset->status, ['Under Maintenance', 'Disposed'])) {
                $statusMessage = $asset->status === 'Under Maintenance' 
                    ? 'under maintenance' 
                    : 'disposed';
                
                return redirect()->back()
                    ->with('error', "Cannot assign asset that is currently {$statusMessage}. Please complete or cancel the maintenance first, or restore the asset from disposal.");
            }
        }

        return $next($request);
    }
}
