<?php
namespace App\Libraries;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
}
