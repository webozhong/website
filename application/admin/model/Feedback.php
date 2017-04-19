<?php
namespace app\admin\model;
use think\Db;
use think\Model;
class Feedback extends Model {
    public static function ShowFeedbackByPage($page){
        $startPage = ($page-1)*10;
        $result = Db::table('feedbacks')
            ->order('id','desc')
            ->limit($startPage,10)
            ->select();
        return $result;
    }
    /**
     * 获取反馈总记录数
     * @return int|string
     */
    public static function GetTotal()
    {
        $num = Db::table('feedbacks')
            ->count();
        return $num;
    }
}