<?php

namespace App\Imports;

use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AssetAssignmentConfirmationImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;
    
    protected $rowCount = 0;
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowCount++;
        
        // Skip if required fields are empty
        if (empty($row['asset_tag']) || empty($row['user_email'])) {
            return null;
        }
        
        // Find asset by asset tag
        $asset = Asset::where('asset_tag', $row['asset_tag'])->first();
        if (!$asset) {
            throw new \Exception("Asset with tag '{$row['asset_tag']}' not found.");
        }
        
        // Find user by email
        $user = User::where('email', $row['user_email'])->first();
        if (!$user) {
            throw new \Exception("User with email '{$row['user_email']}' not found.");
        }
        
        // Check if confirmation already exists
        $existingConfirmation = AssetAssignmentConfirmation::where('asset_id', $asset->id)
                                                         ->where('user_id', $user->id)
                                                         ->first();
        
        if ($existingConfirmation) {
            // Update existing confirmation
            $updateData = [
                'status' => $row['status'] ?? $existingConfirmation->status,
                'priority' => $row['priority'] ?? $existingConfirmation->priority,
                'notes' => $row['notes'] ?? $existingConfirmation->notes
            ];
            
            // Update timestamps based on status
            if (isset($row['status'])) {
                if ($row['status'] === 'confirmed' && !$existingConfirmation->confirmed_at) {
                    $updateData['confirmed_at'] = now();
                } elseif ($row['status'] === 'declined' && !$existingConfirmation->declined_at) {
                    $updateData['declined_at'] = now();
                }
            }
            
            $existingConfirmation->update($updateData);
            
            Log::info('Asset assignment confirmation updated via import', [
                'user_id' => Auth::id(),
                'confirmation_id' => $existingConfirmation->id,
                'asset_tag' => $asset->asset_tag,
                'user_email' => $user->email
            ]);
            
            return $existingConfirmation;
        }
        
        // Create new confirmation
        $confirmationData = [
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'status' => $row['status'] ?? 'pending',
            'priority' => $row['priority'] ?? 'normal',
            'assigned_at' => isset($row['assigned_at']) ? Carbon::parse($row['assigned_at']) : now(),
            'notes' => $row['notes'] ?? null,
            'confirmation_token' => Str::random(32)
        ];
        
        // Set timestamps based on status
        if (isset($row['status'])) {
            if ($row['status'] === 'confirmed') {
                $confirmationData['confirmed_at'] = now();
            } elseif ($row['status'] === 'declined') {
                $confirmationData['declined_at'] = now();
            }
        }
        
        $confirmation = AssetAssignmentConfirmation::create($confirmationData);
        
        Log::info('Asset assignment confirmation created via import', [
            'user_id' => Auth::id(),
            'confirmation_id' => $confirmation->id,
            'asset_tag' => $asset->asset_tag,
            'user_email' => $user->email
        ]);
        
        return $confirmation;
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'asset_tag' => 'required|string',
            'user_email' => 'required|email',
            'status' => 'nullable|in:pending,confirmed,declined',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'assigned_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'asset_tag.required' => 'Asset tag is required.',
            'user_email.required' => 'User email is required.',
            'user_email.email' => 'User email must be a valid email address.',
            'status.in' => 'Status must be one of: pending, confirmed, declined.',
            'priority.in' => 'Priority must be one of: low, normal, high, urgent.',
            'assigned_at.date' => 'Assigned at must be a valid date.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ];
    }
    
    /**
     * Get the number of rows processed
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }
}