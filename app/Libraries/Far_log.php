<?php
namespace App\Libraries;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Far_users;
use DataTables;
abstract class Far_log extends Model
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

    public static function insert($user_id, $type, $additional_log_data){
        $insert_data = array();
        $insert_data['create_dttm'] = date("Y-m-d H:i:s");
        $insert_data['user_id'] = $user_id;
        $insert_data['log_type'] = $type ?? 'info';
        if($additional_log_data){
            $insert_data['log_data_json'] = json_encode($additional_log_data);
        }
        //check $additional_log_data if has key 'meem_code' or 'customer', if yes, insert meem_code or customer to far_log table.
        //convert $additional_log_data to array if it is an JSON string
        $additional_log_data_array = [];
        if(is_string($additional_log_data)){
            $additional_log_data_array = json_decode($additional_log_data, true);
        }

        if(isset($additional_log_data_array['meem_code'])){
            $insert_data['meem_code'] = $additional_log_data_array['meem_code'];
        }elseif(isset($additional_log_data_array['customer'])){
            $insert_data['meem_code'] = $additional_log_data_array['customer'];
        }
        $log_id = DB::table('far_log')->insertGetId($insert_data);
        return $log_id;
    }

    public static function insert_userlog($user_id, $trail_module, $trail_method, $trail_operation, $log_data_json, $meem_code = null){
        $insert_data = array();
        if(!isset($log_data_json['create_dttm'])){
            $insert_data['create_dttm'] = date("Y-m-d H:i:s");
        }else{
            $insert_data['create_dttm'] = $log_data_json['create_dttm'];
        }

        $insert_data['user_id'] = $user_id;
        $insert_data['log_category'] = 'user';
        $insert_data['trail_module'] = $trail_module;
        $insert_data['trail_method'] = $trail_method;
        $insert_data['trail_operation'] = $trail_operation;
        if($log_data_json){
            $insert_data['log_data_json'] = json_encode($log_data_json);
        }

        //check $log_data_json_array if has key 'meem_code' or 'customer' or 'cs_code', if yes, insert meem_code or customer or cs to far_log table.
        if(isset($log_data_json['meem_code'])){
            $insert_data['meem_code'] = $log_data_json['meem_code'];
        }elseif(isset($log_data_json['customer'])){
            $insert_data['meem_code'] = $log_data_json['customer'];
        }elseif (isset($log_data_json['cs_code'])){
            $insert_data['meem_code'] = $log_data_json['cs_code'];
        }

        //if $insert_data['meem_code'] is not set and $meem_code parameter is not null, set $insert_data['meem_code'] to $meem_code parameter
        if(!isset($insert_data['meem_code']) && isset($meem_code)){
            $insert_data['meem_code'] = $meem_code;
        }

        //if $insert_data['app_session'] is set, set $insert_data['app_session'] to $log_data_json['app_session']
        if(isset($log_data_json['app_session'])){
            $insert_data['app_session'] = $log_data_json['app_session'];
        }

        //if $insert_data['device_info'] is set, set $insert_data['device_info'] to $log_data_json['device_info']
        if(isset($log_data_json['device_info'])){
            $insert_data['device_info'] = $log_data_json['device_info'];
        }

        //if $insert_data['device_name'] is set, set $insert_data['device_name'] to $log_data_json['device_name']
        if(isset($log_data_json['device_name'])){
            $insert_data['device_name'] = $log_data_json['device_name'];
        }

        $log_id = DB::table('far_log')->insertGetId($insert_data);
        return $log_id;
    }

    public static function insert_trail($user_id, $trail_module, $trail_method, $trail_operation, $log_data_json){
        $insert_data = array();
        if(!isset($log_data_json['create_dttm'])){
            $insert_data['create_dttm'] = date("Y-m-d H:i:s");
        }else{
            $insert_data['create_dttm'] = $log_data_json['create_dttm'];
        }

        if(!isset($log_data_json['operation_by_user_id'])){
            $log_user_detail = Far_users::get_user_detail($user_id);
            $log_data_json['operation_by_user_id'] = $log_user_detail->user_id;
            $log_data_json['operation_by_fullname'] = $log_user_detail->fullname;
        }

        if(isset($log_data_json['meta_name'])){
            $insert_data['meta_name'] = $log_data_json['meta_name'];
        }
        if(isset($log_data_json['meta_value'])){
            $insert_data['meta_value'] = $log_data_json['meta_value'];
        }

        $insert_data['user_id'] = $user_id;
        $insert_data['log_category'] = 'trail';
        $insert_data['trail_module'] = $trail_module;
        $insert_data['trail_method'] = $trail_method;
        $insert_data['trail_operation'] = $trail_operation;
        if($log_data_json){
            $insert_data['log_data_json'] = json_encode($log_data_json);
        }
        $log_id = DB::table('far_log')->insertGetId($insert_data);
        return $log_id;
    }

    public static function list_all_logs(){
        $list_all_logs = DB::table('far_log')
            ->select(array(
                'far_log.log_id',
                'far_log.user_id',
                'users.fullname',
                'far_log.log_type',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ))
            ->leftJoin('users', 'users.user_id', '=', 'far_log.user_id')
            ->get();
        return $list_all_logs;
    }

    public static function list_all_user_logs(){
        $list_all_logs = DB::table('far_log')
            ->select(array(
                'far_log.log_id',
                'far_log.user_id',
                'users.fullname',
                'far_log.trail_module',
                'far_log.trail_method',
                'far_log.trail_operation',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ))
            ->leftJoin('users', 'users.user_id', '=', 'far_log.user_id')
            ->where('far_log.log_category',"=",'user');

        $columns = request()->input('columns');
        if(isset($columns[0]['search']['regex']) && $columns[0]['search']['regex'] == 'create_dttm_range'){
            $date_range = $columns[0]['search']['value'];
            $x = explode("_", $date_range);
            $start_date = $x[0];
            $end_date = $x[1];
            $list_all_logs->whereDate('create_dttm','<=', $end_date)->whereDate('create_dttm','>=', $start_date);
        }else{
            //initial_start_date
            $initial_start_date = request()->input('initial_start_date');
            $initial_end_date = request()->input('initial_end_date');
            if(isset($initial_start_date)){
                $list_all_logs->whereDate('create_dttm','<=', $initial_end_date)->whereDate('create_dttm','>=', $initial_start_date);
            }
        }
        return $list_all_logs;
    }
    public static function list_all_user_logs_by_user_id($user_id){
        $list_all_logs = DB::table('far_log')
            ->select(array(
                'far_log.log_id',
                'far_log.user_id',
                'users.fullname',
                'far_log.trail_module',
                'far_log.trail_method',
                'far_log.trail_operation',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ))
            ->leftJoin('users', 'users.user_id', '=', 'far_log.user_id')
            ->where('far_log.log_category',"=",'user')
            ->where('far_log.user_id',"=",$user_id)
            ->get();
        return $list_all_logs;
    }

    public static function list_all_audit_trail(){

        $list_all_logs = DB::table('far_log')
            ->select(array(
                'far_log.log_id',
                'far_log.user_id',
                'users.fullname',
                'far_log.trail_module',
                'far_log.trail_method',
                'far_log.trail_operation',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ))
            ->leftJoin('users', 'users.user_id', '=', 'far_log.user_id')
            ->where('far_log.log_category',"=",'trail');


        $columns = request()->input('columns');
        if(isset($columns[0]['search']['regex']) && $columns[0]['search']['regex'] == 'create_dttm_range'){
            $date_range = $columns[0]['search']['value'];
            $x = explode("_", $date_range);
            $start_date = $x[0];
            $end_date = $x[1];
            $list_all_logs->whereDate('create_dttm','<=', $end_date)->whereDate('create_dttm','>=', $start_date);
        }else{
            //initial_start_date
            $initial_start_date = request()->input('initial_start_date');
            $initial_end_date = request()->input('initial_end_date');
            if(isset($initial_start_date)){
                $list_all_logs->whereDate('create_dttm','<=', $initial_end_date)->whereDate('create_dttm','>=', $initial_start_date);
            }
        }

        //filter by user
        if(isset($columns[1]['search']['regex']) && $columns[1]['search']['regex'] == 'filter_user_id'){
            if(isset($columns[1]['search']['value'])){
                $list_user_id = $columns[1]['search']['value'];
                if(isset($list_user_id) && is_array($list_user_id) && count($list_user_id) > 0){
                    foreach($list_user_id as $a => $b){
                        if($a == 0){
                            $list_all_logs->where('far_log.user_id','=', $b);
                        }else{
                            $list_all_logs->orWhere('far_log.user_id','=', $b);
                        }

                    }
                }
            }
        }

        //filter_module
        if(isset($columns[2]['search']['regex']) && $columns[2]['search']['regex'] == 'filter_module'){
            if(isset($columns[2]['search']['value'])){
                $list_modules = $columns[2]['search']['value'];
                if(isset($list_modules) && is_array($list_modules) && count($list_modules) > 0){
                    foreach($list_modules as $a => $b){
                        if($a == 0){
                            $list_all_logs->where('far_log.trail_module','=', $b);
                        }else{
                            $list_all_logs->orWhere('far_log.trail_module','=', $b);
                        }

                    }
                }
            }
        }

        //filter_method
        if(isset($columns[3]['search']['regex']) && $columns[3]['search']['regex'] == 'filter_method'){
            if(isset($columns[3]['search']['value'])){
                $list_methods = $columns[3]['search']['value'];
                if(isset($list_methods) && is_array($list_methods) && count($list_methods) > 0){
                    foreach($list_methods as $a => $b){
                        if($a == 0){
                            $list_all_logs->where('far_log.trail_method','=', $b);
                        }else{
                            $list_all_logs->orWhere('far_log.trail_method','=', $b);
                        }

                    }
                }
            }
        }
        //filter_operation
        if(isset($columns[4]['search']['regex']) && $columns[4]['search']['regex'] == 'filter_operation'){
            if(isset($columns[4]['search']['value'])){
                $list_array = $columns[4]['search']['value'];
                if(isset($list_array) && is_array($list_array) && count($list_array) > 0){
                    foreach($list_array as $a => $b){
                        if($a == 0){

                            $list_all_logs->where('far_log.trail_operation','=', $b);
                        }else{
                            $list_all_logs->orWhere('far_log.trail_operation','=', $b);
                        }
                    }
                }
            }
        }

        return $list_all_logs;
    }

    public static function list_all_audit_trail_by_trail($trail_module, $trail_method){
        $list_all_logs = DB::table('far_log')
            ->select(array(
                'far_log.log_id',
                'far_log.user_id',
                'users.fullname',
                'far_log.trail_module',
                'far_log.trail_method',
                'far_log.trail_operation',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ))
            ->leftJoin('users', 'users.user_id', '=', 'far_log.user_id')
            ->where('far_log.log_category',"=",'trail')
            ->where('far_log.trail_module',"=",$trail_module);

        if(isset($trail_method)){

            $list_all_logs->where('far_log.trail_method',"=",$trail_method);
        }

        $meta_name = request()->input('meta_name');
        if(isset($meta_name)){
            $list_all_logs->where('meta_name',$meta_name);
        }
        $meta_value = request()->input('meta_value');
        if(isset($meta_value)){
            $list_all_logs->where('meta_value',$meta_value);
        }


        return $list_all_logs;
    }

    public static function list_all_user_log_by_trail($trail_module, $trail_method){
        $list_all_logs = DB::table('far_log')
            ->select(array(
                'far_log.log_id',
                'far_log.user_id',
                'users.fullname',
                'far_log.trail_module',
                'far_log.trail_method',
                'far_log.trail_operation',
                'far_log.log_data_json',
                'far_log.create_dttm',
            ))
            ->leftJoin('users', 'users.user_id', '=', 'far_log.user_id')
            ->where('far_log.log_category',"=",'user')
            ->where('far_log.trail_module',"=",$trail_module);

        if(isset($trail_method)){

            $list_all_logs->where('far_log.trail_method',"=",$trail_method);
        }

        $meta_name = request()->input('meta_name');
        if(isset($meta_name)){
            $list_all_logs->where('meta_name',$meta_name);
        }
        $meta_value = request()->input('meta_value');
        if(isset($meta_value)){
            $list_all_logs->where('meta_value',$meta_value);
        }


        return $list_all_logs;
    }

    static function list_audit_trail_module_registered_in_far_log(){
        $list_module = DB::table('far_log')
            ->select('trail_module')
            ->where('log_category', 'trail')->distinct()->get()->toArray();
        if(isset($list_module) && is_array($list_module) && count($list_module) > 0){
            foreach ($list_module as &$module){
                $module->module_nicename = static::get_nicename($module->trail_module);;
            }
        }
        return $list_module;
    }

    static function list_audit_trail_method_registered_in_far_log(){
        $list_method = DB::table('far_log')
            ->select('trail_method')
            ->where('log_category', 'trail')->distinct()->get()->toArray();
        if(isset($list_method) && is_array($list_method) && count($list_method) > 0){
            foreach ($list_method as &$method){
                $method->method_nicename = static::get_nicename($method->trail_method);
            }
        }
        return $list_method;
    }
    static function list_audit_trail_operation_registered_in_far_log(){
        $list_operation = DB::table('far_log')
            ->select('trail_operation')
            ->where('log_category', 'trail')->distinct()->get()->toArray();
        if(isset($list_operation) && is_array($list_operation) && count($list_operation) > 0){
            foreach ($list_operation as &$operation){
                $operation->nicename = static::get_nicename($operation->trail_operation);
            }
        }
        return $list_operation;
    }
    public static function list_audit_trail_module_method_operation_registered_in_far_log(){
        $list_all = [];
        $list_operation = DB::table('far_log')
            ->select('trail_module','trail_method','trail_operation')
            ->where('log_category', 'trail')->distinct()
            ->orderBy('far_log.trail_module', 'ASC')->get()->toArray();
        if(isset($list_operation) && is_array($list_operation) && count($list_operation) > 0){
            foreach ($list_operation as &$operation){
                $operation->nicename = static::get_nicename($operation->trail_operation);
                $list_all[] = $operation->trail_module." - ".$operation->trail_method." - ".$operation->trail_operation;
            }
        }
        return $list_all;
    }

    public static function get_nicename($name){
        if($name == 'countermeasure'){
            $nicename = "Counter Measure";

        }elseif($name == 'dppr'){
            $nicename = 'DPPR';
        }elseif($name == 'customerfeedback'){
            $nicename = 'Customer Feedback';
        }else{
            $nicename = str_replace('_', ' ', $name);
            $nicename = ucwords($nicename);
        }
        return $nicename;
    }


}



?>
