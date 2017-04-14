<?php

namespace app\api\model;
use think\Db;
use think\Model;

class Article extends Model
{
    public static function GetNum(){

        $result = Db::table('articles')->count('*');
        return $result;
    }

    public static function GetArticles($page,$num){

        $result = Db::table('articles')
            ->order('id','desc')
            ->limit($page*10,$num)
            ->select();
        return $result;
    }

    public static function GetArticleById($id){

        $result = Db::table('articles')
            ->where('id',$id)
            ->select();
        return $result;
    }

    /**保存用户反馈信息
     * @param $message
     * @param $number
     * @return int|string
     */
    public static function SaveFeedback($message,$number){
        $data = [
            'message' => $message,
            'number' =>$number
        ];
        $result = Db::table('feedbacks')
            ->insert($data);
        return $result;
    }
}