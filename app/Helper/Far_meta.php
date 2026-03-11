<?php

namespace App\Helper;

use Illuminate\Support\Facades\DB;
class Far_meta
{
    public static function get_meta($meta){
        $meta_detail = DB::table('far_meta')
            ->select('value')
            ->where('meta', $meta)->first();
        if(!isset($meta_detail)){
            DB::table('far_meta')->insertGetId(
                [
                    'meta' => $meta,
                    'value' => NULL
                ]
            );
            $meta_detail = new stdClass();
            $meta_detail->value = NULL;
        }
        return $meta_detail->value;
    }

    public static function update_meta($meta, $value){
        $data = array(
            'value' => $value
        );
        DB::table('far_meta')
            ->where('meta', $meta)  // find your user by their email
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update($data);  // update the record in the DB.
    }
}
?>
