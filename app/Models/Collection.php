<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Metable\Metable;

class Collection extends Model
{
    use HasFactory, Metable;

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    protected $appends = ['token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTokenAttribute()
    {
        return config('blockchains.'.$this->chain_id.'.token');
    }
}
