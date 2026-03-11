<?php

use Illuminate\Support\Facades\DB;
class Far_chance
{
    private static $list_firstname = array(
        'malay' => array(
            'male' => array('Yaakob', 'Sofian', 'Aziz','Haris',"Shukri",'Ali','Razak','Faizal'),
            'female' => array('Fara','Nadia','Suhada','Ashikeen','Fatimah','Anggun','Laila','Akma')
        ),
        'chinese' => array(
            'male' => array('Steven','Peter','Simon','Robert','Henry','Stephen'),
            'female' => array('Cuddy','Sandra','Eva','Emily','Amelia','Sophia','Scarlett')
        ),
        'india' => array(
            'male' => array('Subramanian','Bhavin','Chitaksh','Jaiyush','Bhavin'),
            'female' => array('Kaliani','Charvi', 'Kamala','Kashvi','Tanya','Vihaana')
        )
    );
    private static $list_lastname = array(
        'malay' => array(
            'male' => array('Megat','Muzakir','Umar','Yusof','Anwar','Yazid','Baharom','Ali'),
            'female' => array('Roslan','Jamaludin','Minhad','Hamdan','Adam','Idris','Salleh','Ibrahim'),
        ),
        'chinese' => array(
            'male' => array('Mei','Ting','Zhan','Chow','Yap','Kek','Chua','Law'),
            'female' => array('Hua','Fen','Liling','Zen','Zhan','Ting')
        ),
        'india' => array(
            'male' => array('Viraj','Lakshay','Dhairya','Suveer'),
            'female' => array('Subramanian','Bhavin','Chitaksh','Jaiyush','Bhavin')
        )
    );
    private static $list_gender = array('male','female');
    private static $list_race = array('malay','chinese','india');

    public function __construct()
    {

        //self::$data = 'ghjghjghj';
        self::$MyMember = 'werewrwwwwwwwwwwwwewrwer';



    }
    public static function fullname(){
        //print_r(self::$list_firstname);
        //echo self::$list_firstname['malay']['male'][0];
        $race = static::get_random(self::$list_race);
        $gender = static::get_random(self::$list_gender);
        $firstname = static::get_random(self::$list_firstname[$race][$gender]);
        $lastname = static::get_random(self::$list_lastname[$race][$gender]);
        if($race == 'malay'){
            $middlename = "Binti";
            if($gender == 'male'){ $middlename = "Bin"; }
            $fullname = $firstname." ".$middlename." ".$lastname;
        }elseif($race == 'india'){
            $middlename = "A/P";
            if($gender == 'male'){ $middlename = "A/L"; }
            $fullname = $firstname." ".$middlename." ".$lastname;
        }else{
            $fullname = $firstname." ".$lastname;
        }
        return strtoupper($fullname);
        //return $race;
    }

    public function update_meta($meta, $value){
        $data = array(
            'value' => $value
        );
        DB::table('far_meta')
            ->where('meta', $meta)  // find your user by their email
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update($data);  // update the record in the DB.
    }

    public static function get_random($array){
        $k = array_rand($array);
        return $array[$k];
    }
}
?>
