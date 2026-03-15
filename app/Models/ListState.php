<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListState extends Model
{
    protected $table = 'list_states';

    protected $primaryKey = 'state_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'name',
    ];
}
