<?php
namespace app\api\model;
use think\Db;
use think\Model;

class AppAccess extends Model
{
    public static function Add($openId,$equipment){
        $data = [
            'openid' => $openId,
            'equipment' => $equipment,
        ];
        $result = Db::table('app_access')
            ->insert($data);
        return $result;
    }
}