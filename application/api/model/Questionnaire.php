<?php
namespace app\api\model;
use think\Db;
use think\Model;

class Questionnaire extends Model
{
    public static function add($data){

        $result = Db::table('questionnaires')
            ->insert($data);
        return $result;
    }

    public static function isFill($openid){
        $result = Db::table('questionnaires')
            ->where('openid',$openid)
            ->count();
        return $result;
    }
}