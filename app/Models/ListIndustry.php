<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListIndustry extends Model
{
    protected $table = 'list_industries';

    protected $primaryKey = 'industry_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'name',
    ];
}
