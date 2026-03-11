<?php

namespace App\Helper;

class Far_helper
{
    public static function remove_decimal($number){
        return bcdiv($number, 1, 0);
    }
    public static function decimal_one($number){
        return bcdiv($number, 1, 1);
    }

    public static function server_environment(){
        $output = [];
        $host = request()->getHost();



        $server = 'dev';
        if (str_contains($host, 'uatpmpress')) {
            $server = 'uat';
        }

        if($host == 'press3.test'){
            $server = 'dev';
        }elseif($host == 'devpmpress.perodua.com.my'){
            $server = 'dev';
        }elseif($host == 'uatpmpress.perodua.com.my'){
            $server = 'uat';
        }elseif($host == 'pmpress.perodua.com.my'){
            $server = 'live';
        }


        $output['server'] = $server;
        $output['host'] = $host;
        return $output;
    }

    public static function arrow_indicator($previous_value, $new_value){
        if($previous_value < 0){
            $previous_value = 0;
        }
        $indicator = "same";
        if($new_value > $previous_value){
            $indicator = "higher";
        }elseif($new_value < $previous_value){
            $indicator = "lower";
        }elseif($new_value == $previous_value){
            $indicator = "same";
        }
        return $indicator;
    }
    public static function generateRandomString($length = 10, $characters = NULL) {
        if(!$characters){
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;

    }
    public static function fix_msisdn($phone_number){
        //remove non digit
        $phone_number = preg_replace('/[^0-9.]+/', '', $phone_number);
        //check if possible malaysian phone number
        $first_two = substr($phone_number, 0, 2);
        if($first_two == '01'){
            //might be malaysia phone number, add 6.
            $phone_number = '6'.$phone_number;
        }
        //filter 6060
        $first_four = substr($phone_number, 0, 4);
        if($first_four == '6060'){
            $phone_number = substr($phone_number, 2);
        }
        return $phone_number;
    }
    public static function Gradient($HexFrom, $HexTo, $ColorSteps) {
        $FromRGB['r'] = hexdec(substr($HexFrom, 0, 2));
        $FromRGB['g'] = hexdec(substr($HexFrom, 2, 2));
        $FromRGB['b'] = hexdec(substr($HexFrom, 4, 2));

        $ToRGB['r'] = hexdec(substr($HexTo, 0, 2));
        $ToRGB['g'] = hexdec(substr($HexTo, 2, 2));
        $ToRGB['b'] = hexdec(substr($HexTo, 4, 2));

        $StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($ColorSteps - 1);
        $StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($ColorSteps - 1);
        $StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($ColorSteps - 1);

        $GradientColors = array();

        for($i = 0; $i <= $ColorSteps; $i++) {
            $RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
            $RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
            $RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

            $HexRGB['r'] = sprintf('%02x', ($RGB['r']));
            $HexRGB['g'] = sprintf('%02x', ($RGB['g']));
            $HexRGB['b'] = sprintf('%02x', ($RGB['b']));

            $GradientColors[] = implode(NULL, $HexRGB);
        }
        $GradientColors = array_filter($GradientColors, 'static::len');
        return $GradientColors;
    }

    public static function invertColor($hex) {

        $ihex = \dechex($hex);

        $r = \dechex(255 - \round(\hexdec(\substr($ihex, 0,2))));
        $g = \dechex(255 - \round(\hexdec(\substr($ihex, 2,2))));
        $b = \dechex(255 - \round(\hexdec(\substr($ihex, 4,2))));

        // If the color (rgb) has less than 2 characters, pad with zero
        $padZero = function ($str) {
            return \str_pad($str, 2, 0, \STR_PAD_LEFT);
        };

        // Pad with zero
        $output = $padZero($r) . $padZero($g) . $padZero($b);
        if($output == "ffffff"){
            $output = "000000";
        }
        return $output;

    }

    public static function len($val){
        return (strlen($val) == 6 ? true : false );
    }

    public static function closest($number, $array){
        //find distances to number
        $dist = array_map(
            function($val) use ($number) {
                return abs($number - $val);
            },
            $array);
        //flip array so distance is key
        $dist = array_flip($dist);
        //sort distance by key
        ksort($dist);

        //get key for shortest distance
        $key = array_values($dist)[0];

        return $array[$key];
    }

    public static function findMajorityElement($arr)
    {

        sort($arr);
        $n = count($arr);
        $majorityCount = ceil($n / 2);
        $current = null;
        $count = 0;

        foreach ($arr as $num) {
            if ($num !== $current) {
                $current = $num;
                $count = 1;
            } else {
                $count++;
            }
            if ($count >= $majorityCount) {
                return $num;
            }
        }
        return null;
    }

}
