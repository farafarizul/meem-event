<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPriceDaily extends Model
{
    protected $table = 'gold_price_daily';

    protected $primaryKey = 'gold_price_daily_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'gold_price_date',
        'sell_price',
        'buy_price',
        'open_price',
        'close_price',
        'highest_price',
        'lowest_price',
        'reason_from_ai',
    ];

    protected $casts = [
        'gold_price_date' => 'date',
        'sell_price'      => 'decimal:2',
        'buy_price'       => 'decimal:2',
        'open_price'      => 'decimal:2',
        'close_price'     => 'decimal:2',
        'highest_price'   => 'decimal:2',
        'lowest_price'    => 'decimal:2',
    ];
}
