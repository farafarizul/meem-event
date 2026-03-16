<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListIdentificationType extends Model
{
    protected $table = 'list_identification_type';

    protected $primaryKey = 'identification_type_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'name',
    ];
}
