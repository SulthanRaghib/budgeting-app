<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'start_date',
        'target_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = ['user_name'];

    public function getUserNameAttribute()
    {
        return $this->user?->name;
    }
}
