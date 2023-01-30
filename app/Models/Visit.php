<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'visit_code',
        'lead_id',
        'note',
        'visit_date',
        'status',
        'sales_id',
        'created_by',
        'project_code',
    ];


    public function lead() {
        return $this->hasOne(Lead::class, 'id', 'lead_id');
    }

    public function sales() {
        return $this->hasOne(User::class, 'id', 'sales_id');
    }

    public function createBy() {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

}
