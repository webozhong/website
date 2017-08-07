<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class AppAccess extends Model
{
    /**
     * 获得回访用户
     * @param $openid
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getActiveUsers($openid,$startTime,$endTime)
    {
        $activeUsers = Db::table('app_access')
            ->where('openid', $openid)
            ->whereTime('time', 'between', [$startTime, $endTime])
            ->distinct(true)
            ->field('openid')
            ->select();
        return $activeUsers;
    }
}