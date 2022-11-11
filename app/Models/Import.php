<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $fillable = ['chain_id', 'import_at'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
