<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Department extends Model
{
    use ActivityLoggable;
    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'parent_id',
    ];

    // Relationships
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Parent-child relationships
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    // Get all descendants (recursive)
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Get all ancestors (recursive)
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    // Check if department is a root department (no parent)
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    // Check if department is a leaf department (no children)
    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }
}
