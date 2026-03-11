<?php
namespace App\Libraries;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Libraries\Far_log;
use App\Helper\Far_meta;
use App\Helper\Far_helper;
abstract class Far_users extends Model
{
    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function get_user_detail($user_id){
        $user_detail = DB::table('users')->where('user_id', $user_id)->first();
        if(isset($user_detail->mobile_number) && strlen($user_detail->mobile_number) > 3){
            $msisdn = Far_helper::fix_msisdn($user_detail->mobile_number);
            $user_detail->mobile_number = $msisdn;
        }
        return $user_detail;
    }
    public static function get_user_detail_by_column($column, $value){
        $user_detail = DB::table('users')->where($column, $value)->first();
        return $user_detail;
    }
    public static function get_user_value_by_column($column_name, $column_value, $output_column){
        $user_detail = DB::table('users')->select($output_column)->where($column_name, $column_value)->pluck($output_column)->toArray()[0];
        return $user_detail;
    }

    public static function get_from_view($user_id){
        $user_detail = DB::table('users')
            ->select(array(
                'users.user_id',
                'users.user_group_id',
                'users_group.user_group_name',
                'users.fullname',
                'users.email',
                'users.mobile_number',
                'users.shift_id',
                'shift_detail.shift_name',
                'users.line_id',
                'line_detail.line_name',

                'users.force_change_password',
                'users.last_login_dttm',
                'users.last_login_ip',
                'users.failed_login_attempt',
                'users.notify_password_expired_dttm',
                'users.password_expired_dttm',
                'users.user_status',

                'users.created_at',
            ))
            ->where('user_id', $user_id)
            ->leftJoin('users_group', 'users.user_group_id', '=', 'users_group.user_group_id')
            ->leftJoin('shift_detail', 'users.shift_id', '=', 'shift_detail.shift_id')
            ->leftJoin('line_detail', 'users.line_id', '=', 'line_detail.line_id')
            ->first();
        //$msisdn = Far_helper::fix_msisdn($user_detail->mobile_number);
        //$user_detail->mobile_number = $msisdn;
        return $user_detail;
    }

    public static function insert_user_detail($user_data = array()){

        if(!isset($user_data['created_at'])){
            $user_data['created_at'] = date("Y-m-d H:i:s");
        }
        $user_data['raw_password'] = $user_data['password'];
        $user_data['password'] = Hash::make($user_data['password']);


        $user_id = DB::table('users')->insertGetId($user_data);
        return $user_id;
    }
    public static function list_all_users(){
        $list_all_operator = DB::table('users')
            ->where('users.user_status', '!=', 'deleted')
            ->orWhere('users.user_status', '!=', 'locked')
            ->leftJoin('shift_detail', 'users.shift_id', '=', 'shift_detail.shift_id')
            ->leftJoin('line_detail', 'users.line_id', '=', 'line_detail.line_id')
            ->get();


        return $list_all_operator;
    }
    public static function check_is_email_exists($email){
        $user_detail = DB::table('users')->where('email', $email)->first();
        if($user_detail){
            return TRUE;
        }else{
            return false;
        }
    }
    public static function force_reset_password($user_id){

        $random_password = Far_helper::generateRandomString(8);

        static::update_user_detail('user_id', $user_id, 'raw_password', $random_password);
        static::update_user_detail('user_id', $user_id, 'password', Hash::make($random_password));
        static::update_user_detail('user_id', $user_id, 'force_change_password', 'yes');



        return $random_password;
    }
    public static function change_password($user_id, $password){
        static::update_user_detail('user_id', $user_id, 'raw_password', $password);
        static::update_user_detail('user_id', $user_id, 'password', Hash::make($password));
    }
    public static function update_user_detail($key, $key_value, $column, $value){
        $data = array(
            $column => $value
        );
        DB::table('users')
            ->where($key, $key_value)  // find your user by their email
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update($data);  // update the record in the DB.
    }
    public static function delete_user($user_id){
        $user_detail = static::get_user_detail($user_id);
        $data = array(
            'email' => 'deleted-'.$user_detail->email,
            'user_status' => 'deleted'
        );
        DB::table('users')
            ->where('user_id', $user_id)  // find your user by their email
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update($data);  // update the record in the DB.

        $user_detail = Auth::user();

        $log_data = array();
        $log_data['deleted_user_id'] = $user_id;
        Far_log::insert($user_detail->user_id, 'cms_delete_user', $log_data);
    }
    public static function list_users_by_user_group_id($user_group_id){
        $list_all_admin = DB::table('users')
            ->where('user_group_id', $user_group_id)
            ->where('user_status', 'active')
            ->get();
        return $list_all_admin;
    }
    public static function count_password_retention($user_id, $password){
        $list_all_user_password = DB::table('password_retention')
            ->where('user_id', $user_id)
            ->get();

        $count_same_password = 0;
        if(count($list_all_user_password) > 0){
            foreach($list_all_user_password as $a => $b){
                if (Hash::check($password, $b->password)){
                    $count_same_password++;
                }
            }
        }
        //$results = DB::select("SELECT COUNT(password_retention_id) AS total_count FROM password_retention WHERE user_id = ? AND password = ?", [$user_id, $hashed_password])[0];
        return $count_same_password;
    }
    public static function insert_password_retention($user_id, $hashed_password){
        $user_data = array();

        $user_data['user_id'] = $user_id;
        $user_data['password'] = $hashed_password;
        $user_data['create_dttm'] = date("Y-m-d H:i:s");

        $user_id = DB::table('password_retention')->insertGetId($user_data);
        return $user_id;
    }

    public static function list_all_sessions($user_id){
        $list_all_sessions = DB::table('sessions AS s')
            ->select([
                's.*',
                'u.fullname'
            ])
            ->leftJoin('users AS u', 's.user_id', '=', 'u.user_id')
            ->where('s.user_id', $user_id)
            ->get()->toArray();


        return $list_all_sessions;
    }


}



?>
