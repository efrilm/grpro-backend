<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Date extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'lead_id',
        'date_add',
        'date_follow_up',
        'date_will_visit',
        'date_already_visit',
        'date_reservation',
        'date_booking',
        'date_sold',
        'date_refund',
    ];
}
