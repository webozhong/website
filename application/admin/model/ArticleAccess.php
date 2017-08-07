<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class ArticleAccess extends Model
{

    //时间内的用户pv
    public static function userPv($startTime,$endTime){
        $result = Db::table('article_access')
            //->whereTime('time', 'between', [$startTime, $endTime])
            ->select();
        return $result;
    }
}