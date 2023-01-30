<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_code',
        'name',
        'no_whatsapp',
        'address',
        'note',
        'source',
        'sales_id',
        'created_by',
        'home_id',
        'day',
        'status',
        'payment_method',
        'project_code',
    ];

    public function sales()
    {
        return $this->hasOne(User::class, 'id', 'sales_id');
    }

    public function createBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function tracking()
    {
        return $this->hasMany(Tracking::class, 'lead_id', 'id');
    }

    public function date()
    {
        return $this->hasOne(Date::class, 'lead_id', 'id');
    }

    public function fee() 
    {
        return $this->hasOne(Fee::class, 'lead_id', 'id');
    }

    public function home() {
        return $this->hasOne(Home::class, 'id', 'home_id');
    }

    public function payment() {
        return $this->hasOne(Payment::class, 'lead_id', 'id');
    }

}
