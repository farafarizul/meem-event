<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarLog extends Model
{
    protected $table      = 'far_log';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'meem_code',
        'log_category',
        'trail_module',
        'trail_method',
        'trail_operation',
        'log_data_json',
        'create_dttm',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'meem_code', 'meem_code');
    }
}
