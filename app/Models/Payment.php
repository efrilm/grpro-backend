<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_code',
        'lead_id',
        'discount_price',
        'downpayment',
        'discount_downpayment',
        'downpayment_paid',
        'subtotal',
    ];
}
