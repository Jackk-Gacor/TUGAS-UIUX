<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'amount',
        'status',
        'transaction_ref',
        'qris_proof_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
