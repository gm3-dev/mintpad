<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $fillable = ['chain_id', 'import_at'];
    protected $appends = ['period', 'transaction_count'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPeriodAttribute()
    {
        return date('m-Y', strtotime($this->import_at));
    }

    public function getTransactionCountAttribute()
    {
        return $this->transactions->count();
    }
}
