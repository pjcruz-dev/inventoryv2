<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'asset_id',
        'from_user_id',
        'to_user_id',
        'from_department',
        'to_department',
        'transfer_date',
        'remarks',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
