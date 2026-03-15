<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListCountry extends Model
{
    protected $table = 'list_countries';

    protected $primaryKey = 'country_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'name',
    ];
}
