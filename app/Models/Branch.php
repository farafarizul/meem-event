<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'branch_name',
        'branch_code',
        'branch_phone',
        'branch_address',
        'postcode',
        'state',
        'area',
        'person_in_charge_name',
        'person_in_charge_phone',
        'branch_type',
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'branch_id', 'branch_id');
    }
}
