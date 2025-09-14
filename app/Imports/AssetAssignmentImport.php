<?php

namespace App\Imports;

use App\Models\AssetAssignment;
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
use Carbon\Carbon;

class AssetAssignmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
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
        
        // Check if assignment already exists
        $existingAssignment = AssetAssignment::where('asset_id', $asset->id)
                                           ->where('user_id', $user->id)
                                           ->where('status', 'active')
                                           ->first();
        
        if ($existingAssignment) {
            // Update existing assignment
            $existingAssignment->update([
                'assigned_date' => isset($row['assigned_date']) ? Carbon::parse($row['assigned_date']) : $existingAssignment->assigned_date,
                'return_date' => isset($row['return_date']) ? Carbon::parse($row['return_date']) : $existingAssignment->return_date,
                'notes' => $row['notes'] ?? $existingAssignment->notes
            ]);
            
            Log::info('Asset assignment updated via import', [
                'user_id' => Auth::id(),
                'assignment_id' => $existingAssignment->id,
                'asset_tag' => $asset->asset_tag,
                'user_email' => $user->email
            ]);
            
            return $existingAssignment;
        }
        
        // Create new assignment
        $assignment = AssetAssignment::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'assigned_by' => Auth::id(),
            'assigned_date' => isset($row['assigned_date']) ? Carbon::parse($row['assigned_date']) : now(),
            'return_date' => isset($row['return_date']) ? Carbon::parse($row['return_date']) : null,
            'status' => 'active',
            'notes' => $row['notes'] ?? null
        ]);
        
        // Update asset status
        $asset->update([
            'status' => 'assigned',
            'assigned_to' => $user->id
        ]);
        
        Log::info('Asset assignment created via import', [
            'user_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'asset_tag' => $asset->asset_tag,
            'user_email' => $user->email
        ]);
        
        return $assignment;
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'asset_tag' => 'required|string',
            'user_email' => 'required|email',
            'assigned_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:assigned_date',
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
            'assigned_date.date' => 'Assigned date must be a valid date.',
            'return_date.date' => 'Return date must be a valid date.',
            'return_date.after_or_equal' => 'Return date must be after or equal to assigned date.',
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