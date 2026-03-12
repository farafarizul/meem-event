<?php
namespace App\Libraries;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helper\Far_helper;
abstract class Far_gold extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /*
    create a percentage. there are multiple thresholds for the percentage. if gss_balance is below 1, the threshold  is 1.
    $gss_threshold_array = [0.01, 0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000];
    gss_balance is a gold in a gram for example 0.0094

    threshold must have the following values: 0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000
    $threshold_array = [0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000];

    progress_value must use 4 decimal places
    $gss_user_progress_value = number_format($gss_user_progress_threshold - $gss_balance, 4);
    $gss_user_progress_percentage = ($gss_balance / $gss_user_progress_threshold) * 100;

    $gss_progress = [
        'balance' => $gss_balance,
        'threshold' => $gss_user_progress_threshold,
        'progress_value' => (double)$gss_user_progress_value,
        'progress_percentage' => $gss_user_progress_percentage,
        'progress_bar_percentage' => $gss_user_progress_percentage / 100
    ];
    */
    public static function gold_progress_detail($gss_balance){
        $gss_threshold_array = [0.01, 0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000];
        $gss_user_progress_threshold = 1; // default threshold
        foreach ($gss_threshold_array as $t) {
            if ($gss_balance < $t) {
                $gss_user_progress_threshold = $t;
                break;
            }
        }
        //progress_value must use 4 decimal places
        $gss_user_progress_value = number_format($gss_user_progress_threshold - $gss_balance, 4);
        $gss_user_progress_percentage = ($gss_balance / $gss_user_progress_threshold) * 100;

        $gss_progress = [
            'balance' => $gss_balance,
            'threshold' => $gss_user_progress_threshold,
            'progress_value' => (double)$gss_user_progress_value,
            'progress_percentage' => $gss_user_progress_percentage,
            'progress_bar_percentage' => $gss_user_progress_percentage / 100
        ];
        return $gss_progress;

    }

    /*
     * Get latest gold price from database. table is gold_price
     */
    public static function get_latest_gold_price()
    {
        $gold_price = DB::table('gold_price')->orderBy('created_at', 'desc')->first();
        return $gold_price;
    }
    /*
     * Gold value detail is a combination of the user's gss balance, the latest gold price, and the calculated gold value of the user's gss balance. The gold value is calculated by multiplying the user's gss balance with the latest gold price. The function should return an array with the following structure:
     * "gss_detail": {
            "balance": 0.0094,
            "gold_price": 644,
            "gold_value": 6.05
        }
     */
    public static function gold_value_detail($gss_balance)
    {
        $latest_gold_price = self::get_latest_gold_price();
        $gold_price = $latest_gold_price ? (double)number_format($latest_gold_price->buy_price, 2, '.', '') : 0;
        $gss_gold_value = (double)number_format($gss_balance * $gold_price, 2, '.', '');
        //$gss_gold_value must be in currency format with 2 decimal places and use dot as decimal separator. Don't round up or round down the gold value, just format it to 2 decimal places.
        $gss_gold_value = Far_helper::truncate_decimal($gss_gold_value, 2);
        
        $gss_detail = [
            'balance' => $gss_balance,
            'gold_price' => $gold_price,
            'gold_value' => $gss_gold_value
        ];
        return $gss_detail;
    }
}
