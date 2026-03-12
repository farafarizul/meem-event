<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SilverPrice extends Model
{
    protected $table = 'silver_price';

    protected $primaryKey = 'silver_price_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'type',
        'product',
        'unit',
        'currency',
        'sell_price',
        'buy_price',
        'timezone',
        'last_updated',
    ];

    protected $casts = [
        'sell_price'   => 'decimal:2',
        'buy_price'    => 'decimal:2',
        'last_updated' => 'datetime',
    ];
}
