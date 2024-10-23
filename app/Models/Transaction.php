<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['import_id', 'block', 'from', 'to', 'amount', 'fee', 'price', 'method', 'transaction_at'];

    public function import()
    {
        $this->belongsTo(Import::class);
    }
}
