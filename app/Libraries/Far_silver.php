<?php
namespace App\Libraries;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helper\Far_helper;
abstract class Far_silver extends Model
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
    create a percentage. there are multiple thresholds for the percentage. if sss_balance is below 1, the threshold  is 1.
    $sss_threshold_array = [0.01, 0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000];
    sss_balance is a silver in a gram for example 0.0094

    threshold must have the following values: 0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000
    $threshold_array = [0.1,0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000];

    progress_value must use 4 decimal places
    $sss_user_progress_value = number_format($sss_user_progress_threshold - $sss_balance, 4);
    $sss_user_progress_percentage = ($sss_balance / $sss_user_progress_threshold) * 100;

    $sss_progress = [
        'balance' => $sss_balance,
        'threshold' => $sss_user_progress_threshold,
        'progress_value' => (double)$sss_user_progress_value,
        'progress_percentage' => $sss_user_progress_percentage,
        'progress_bar_percentage' => $sss_user_progress_percentage / 100
    ];
    */
    public static function silver_progress_detail($sss_balance)
    {
        $sss_threshold_array = [0.01, 0.1, 0.5, 1, 10, 100, 1000, 2000, 3000, 5000, 10000];
        $sss_user_progress_threshold = 1; // default threshold
        foreach ($sss_threshold_array as $t) {
            if ($sss_balance < $t) {
                $sss_user_progress_threshold = $t;
                break;
            }
        }
        //progress_value must use 4 decimal places
        $sss_user_progress_value = number_format($sss_user_progress_threshold - $sss_balance, 4);
        $sss_user_progress_percentage = ($sss_balance / $sss_user_progress_threshold) * 100;

        $sss_progress = [
            'balance' => $sss_balance,
            'threshold' => $sss_user_progress_threshold,
            'progress_value' => (double)$sss_user_progress_value,
            'progress_percentage' => $sss_user_progress_percentage,
            'progress_bar_percentage' => $sss_user_progress_percentage / 100
        ];
        return $sss_progress;

    }

    /*
     * Get latest silver price from database. table is silver_price
     */
    public static function get_latest_silver_price()
    {
        $silver_price = DB::table('silver_price')->orderBy('created_at', 'desc')->first();
        return $silver_price;
    }

    /*
     * silver value detail is a combination of the user's sss balance, the latest silver price, and the calculated silver value of the user's sss balance. The silver value is calculated by multiplying the user's sss balance with the latest silver price. The function should return an array with the following structure:
     * "sss_detail": {
            "balance": 0.0094,
            "silver_price": 644,
            "silver_value": 6.05
        }
     */
    public static function silver_value_detail($sss_balance)
    {
        $latest_silver_price = self::get_latest_silver_price();
        $silver_price = $latest_silver_price ? (double)number_format($latest_silver_price->buy_price, 2, '.', '') : 0;
        $sss_silver_value = (double)number_format($sss_balance * $silver_price, 2, '.', '');
        //$sss_silver_value must be in currency format with 2 decimal places and use dot as decimal separator. Don't round up or round down the silver value, just format it to 2 decimal places.
        $sss_silver_value = Far_helper::truncate_decimal($sss_silver_value, 2);

        //$sss_balance must be in 4 decimal format.
        $sss_balance = Far_helper::truncate_decimal($sss_balance, 4);

        $sss_detail = [
            'balance' => $sss_balance,
            'silver_price' => $silver_price,
            'silver_value' => $sss_silver_value
        ];
        return $sss_detail;
    }
}
