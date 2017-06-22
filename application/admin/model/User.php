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
            ->order('last_login','desc')
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
}