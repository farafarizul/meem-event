<?php
namespace App\Helper;
class Far_date
{
    public static function add_minutes_to_dttm($minutes_to_add, $dttm = NULL, $output_format = "Y-m-d H:i:s"){
        if(!$dttm){
            $dttm = date("Y-m-d H:i:s");
        }
        $time = new DateTime($dttm);
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

        $stamp = $time->format($output_format);
        return $stamp;
    }
    public static function subtract_minutes_to_dttm($minutes_to_add, $dttm = NULL, $output_format = "Y-m-d H:i:s"){
        if(!$dttm){
            $dttm = date("Y-m-d H:i:s");
        }
        $time = new DateTime($dttm);
        $time->sub(new DateInterval('PT' . $minutes_to_add . 'M'));

        $stamp = $time->format($output_format);
        return $stamp;
    }
    public static function convert_iso8601($date_iso8601, $new_format){
        $fixed = date($new_format, strtotime(substr($date_iso8601,0,10)));
        return $fixed;
    }
    public static function convert_date_format($from_date, $from_format, $new_format){
        //echo $from_date; exit();
        $outgoing_date = "";
        if(isset($from_date) && strlen($from_date) > 3){
            if($from_format == 'Y-m-d H:i:s'){
                if(str_contains($from_date, '.')) {
                    $from_date = substr($from_date, 0, -4);
                }
            }

            $converted_date = DateTime::createFromFormat($from_format, $from_date);

            $outgoing_date = $converted_date->format($new_format);

            //echo $from_date.' -- '.$from_format.' -- '.$new_format.' -- '.$outgoing_date; echo "<hr>";
        }

        return $outgoing_date;
    }
    public static function difference_between_dates_in_minutes($start_dttm, $end_dttm){
        $datetime1 = strtotime($start_dttm);
        $datetime2 = strtotime($end_dttm);
        $interval  = abs($datetime2 - $datetime1);
        $minutes   = round($interval / 60);
        return $minutes;
    }

    public static function seconds_to_dttm($seconds){
        $seconds = round($seconds);
        $output = sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
        return $output;
    }

    public static function list_dates_between_two_dates($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public static function list_dates_between_two_dates_until_today($first, $step = '+1 day', $output_format = 'Y-m-d' ) {

        $dates = array();
        $current = strtotime($first);

        if(date("Y-m", strtotime($first)) == date("Y-m")){
            $last = strtotime(date("Y-m-d"));
        }else{
            $last = strtotime(date("Y-m-t", strtotime($first)));
        }


        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public static function list_month_between_two_dates_until_today($start_date) {

        $dates = array();
        $start    = (new DateTime($start_date))->modify('first day of this month');



        if(date("Y-m", strtotime($start_date)) == date("Y-m")){
            $last = date("Y-m-d");
        }else{

            $last = date("Y-m-t", strtotime($start->format("Y").'-12'));
        }

        $end      = (new DateTime($last))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $dates[] = $dt->format("Y-m-01");
        }

        return $dates;
    }

    public static function add_days_to_date($dttm, $days, $format = "Y-m-d"){
        // Create a DateTime object
        $date = new DateTime($dttm);

        // Add 7 days to the date
        $date->modify("+$days days");

        // Format and output the modified date
        return $date->format($format);
    }
    public static function subtract_days_to_date($dttm, $days, $format = "Y-m-d"){
        // Create a DateTime object
        $date = new DateTime($dttm);

        // Add 7 days to the date
        $date->modify("-$days days");

        // Format and output the modified date
        return $date->format($format);
    }
    public static function count_days_between_two_dates($dttm_1, $dttm_2){
        $datetime1 = new DateTime($dttm_1);
        $datetime2 = new DateTime($dttm_2 ?? date("Y-m-d"));

        $interval = $datetime1->diff($datetime2);

        return $interval->days;
    }


    public static function define_day_night_shift_date($dppr_date, $die_setting_start_dttm, $production_finish_dttm){
        $output = [];
        //check if same day or next day
        $same_day = "no";
        $die_setting_start_date = static::convert_date_format($die_setting_start_dttm, 'Y-m-d H:i:s', "Y-m-d");
        $production_finish_date = static::convert_date_format($production_finish_dttm, 'Y-m-d H:i:s', "Y-m-d");
        if($die_setting_start_date == $production_finish_date){
            $same_day = "yes";
        }else{
        }

        //check if day shift
        $die_setting_start_hour = static::convert_date_format($die_setting_start_dttm, 'Y-m-d H:i:s', "H");
        $production_finish_hour = static::convert_date_format($production_finish_dttm, 'Y-m-d H:i:s', "H");

        $shift_day_night = "day";
        if(
            ($die_setting_start_hour >= 20 && $production_finish_hour <= 23) ||
            ($die_setting_start_hour >= 0 && $production_finish_hour <= 07)
        ){
            //probably night shift
            $shift_day_night = "night";
        }

        $output['date']['dppr_date'] = $dppr_date;
        $output['date']['same_day'] = $same_day;
        $output['date']['die_setting_start_date'] = $die_setting_start_date;
        $output['date']['production_finish_date'] = $production_finish_date;

        $output['time']['shift_day_night'] = $shift_day_night;
        $output['time']['die_setting_start_hour'] = $die_setting_start_hour;
        $output['time']['production_finish_hour'] = $production_finish_hour;

        $output['final']['dppr_date_shift'] = $dppr_date;
        $output['final']['shift_day_night'] = $shift_day_night;

        return $output;
    }

    public static function determine_current_shift($dttm = null){
        $output = [];
        if(!$dttm){
            $dttm = date("Y-m-d H:i:s");
        }

        $dttm_date = static::convert_date_format($dttm, 'Y-m-d H:i:s', "Y-m-d");


        $hour = static::convert_date_format($dttm, 'Y-m-d H:i:s', "H");
        if(
            ($hour >= 20 && $hour <= 23) ||
            ($hour >= 0 && $hour <= 7)
        ){
            $shift_day_night = "night";

            if($hour >= 00 && $hour < 8){

                $start_dttm = static::convert_date_format(static::subtract_days_to_date($dttm,1,"Y-m-d H:i:s"), 'Y-m-d H:i:s', "Y-m-d")." 20:00:00";
                $end_dttm = static::convert_date_format($dttm, 'Y-m-d H:i:s', "Y-m-d")." 08:00:00";
            }else{
                $start_dttm = static::convert_date_format($dttm, 'Y-m-d H:i:s', "Y-m-d")." 20:00:00";

                $end_dttm = static::convert_date_format(static::add_days_to_date($dttm,1,"Y-m-d H:i:s"), 'Y-m-d H:i:s', "Y-m-d")." 08:00:00";
            }
        }else{
            $shift_day_night = "day";
            $start_dttm = static::convert_date_format($dttm, 'Y-m-d H:i:s', "Y-m-d")." 08:00:00";
            $end_dttm = static::convert_date_format($dttm, 'Y-m-d H:i:s', "Y-m-d")." 20:00:00";
        }



        $output['date'] = $dttm;
        $output['dppr_date_shift'] = static::convert_date_format($start_dttm, 'Y-m-d H:i:s', "Y-m-d");
        $output['shift_day_night'] = $shift_day_night;
        $output['start_dttm'] = $start_dttm;
        $output['end_dttm'] = $end_dttm;

        //date range


        return $output;
    }

    public static function calculate_setting_time($dppr_date, $die_setting_start_time, $die_setting_finish_time){
        $output = [];

        $die_setting_start_hour = static::convert_date_format($die_setting_start_time, 'H:i:s', "H");
        //echo $die_setting_start_hour; exit();
        if($die_setting_start_hour >= 8 && $die_setting_start_hour <= 19){
            $start_dttm = $dppr_date.' '.$die_setting_start_time;
        }elseif($die_setting_start_hour >=20 && $die_setting_start_hour <= 23){
            $start_dttm = $dppr_date.' '.$die_setting_start_time;
        }elseif($die_setting_start_hour >=0 && $die_setting_start_hour <= 7){
            $start_dttm = static::add_days_to_date($dppr_date,1).' '.$die_setting_start_time;
        }


        $die_setting_finish_hour = static::convert_date_format($die_setting_finish_time, 'H:i:s', "H");
        if($die_setting_finish_hour >= 8 && $die_setting_finish_hour <= 19){
            $end_dttm = $dppr_date.' '.$die_setting_finish_time;
        }elseif($die_setting_finish_hour >=20 && $die_setting_finish_hour <= 23){
            $end_dttm = $dppr_date.' '.$die_setting_finish_time;
        }elseif($die_setting_finish_hour >=0 && $die_setting_finish_hour <= 7){
            $end_dttm = static::add_days_to_date($dppr_date,1).' '.$die_setting_finish_time;
        }

        //start_date
        $final_start_date = static::convert_date_format($start_dttm, 'Y-m-d H:i:s', "Y-m-d");
        $final_end_date = static::convert_date_format($end_dttm, 'Y-m-d H:i:s', "Y-m-d");

        $difference_in_minutes = static::difference_between_dates_in_minutes($start_dttm, $end_dttm);


        $output['dppr_date'] = $dppr_date;
        $output['start_date'] = $final_start_date;
        $output['start_time'] = static::convert_date_format($start_dttm, 'Y-m-d H:i:s', "H:i:s");
        $output['start_dttm'] = $start_dttm;
        $output['end_date'] = $final_end_date;
        $output['end_time'] = static::convert_date_format($end_dttm, 'Y-m-d H:i:s', "H:i:s");
        $output['end_dttm'] = $end_dttm;
        $output['difference_in_minutes'] = $difference_in_minutes;


        return $output;
    }
}
?>
