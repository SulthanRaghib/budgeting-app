<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    protected $guarded = [];

    protected $appends = ['user_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savingGoal()
    {
        return $this->belongsTo(SavingGoal::class);
    }

    public function getUserNameAttribute()
    {
        return $this->user?->name;
    }
}
