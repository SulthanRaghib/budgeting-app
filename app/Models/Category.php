<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color',
        'icon',
    ];
    protected $appends = ['user_name'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function budget()
    {
        return $this->hasOne(\App\Models\Budget::class);
    }

    public function getUserNameAttribute()
    {
        return $this->user?->name;
    }
}
