<?php

namespace App\Imports;

use App\Models\AssetCategory;
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

class AssetCategoryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
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
        
        // Skip if name is empty
        if (empty($row['name'])) {
            return null;
        }
        
        // Check if category already exists
        $existingCategory = AssetCategory::where('name', $row['name'])->first();
        
        if ($existingCategory) {
            // Update existing category
            $existingCategory->update([
                'description' => $row['description'] ?? $existingCategory->description
            ]);
            
            Log::info('Asset category updated via import', [
                'user_id' => Auth::id(),
                'category_id' => $existingCategory->id,
                'category_name' => $existingCategory->name
            ]);
            
            return $existingCategory;
        }
        
        // Create new category
        $category = AssetCategory::create([
            'name' => $row['name'],
            'description' => $row['description'] ?? null
        ]);
        
        Log::info('Asset category created via import', [
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'category_name' => $category->name
        ]);
        
        return $category;
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000'
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 100 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.'
        ];
    }
    
    /**
     * Get the number of rows processed
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}