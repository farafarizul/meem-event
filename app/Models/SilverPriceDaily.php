<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SilverPriceDaily extends Model
{
    protected $table = 'silver_price_daily';

    protected $primaryKey = 'silver_price_daily_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'silver_price_date',
        'sell_price',
        'buy_price',
        'open_price',
        'close_price',
        'highest_price',
        'lowest_price',
        'candle_direction',
        'reason_from_ai',
    ];

    protected $casts = [
        'silver_price_date' => 'date',
        'sell_price'        => 'decimal:2',
        'buy_price'         => 'decimal:2',
        'open_price'        => 'decimal:2',
        'close_price'       => 'decimal:2',
        'highest_price'     => 'decimal:2',
        'lowest_price'      => 'decimal:2',
    ];
}
