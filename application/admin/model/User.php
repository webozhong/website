<?php
namespace app\admin\model;
use think\Db;
use think\Model;
class User extends Model {
    /**
     * 根据page参数显示记录每页10条
     * @param $page
     * @return array
     */
    public static function ShowUsersByPage($page){
        $startPage = ($page-1)*10;
        $result = Db::table('users')
            ->order('registered','desc')
            ->limit($startPage,10)
            ->select();
        return $result;
    }


    /**
     * 获取文章总数
     * @return int|string
     */
    public static function GetTotal()
    {
        $num = Db::table('users')
            ->count();
        return $num;
    }

    /**
     * 获得指定时间段内的新注册用户
     * @param $startTime
     * @param $endTime
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getNewUsers($startTime,$endTime){
        $newUsers = Db::table('users')
            ->whereTime('registered','between',[$startTime,$endTime])
            ->field('openid')
            ->select();
        return $newUsers;
    }
    public static function getOldUsers($endTime){
        $newUsers = Db::table('users')
            ->whereTime('registered','<',$endTime)
            ->field('openid')
            ->select();
        return $newUsers;
    }

}