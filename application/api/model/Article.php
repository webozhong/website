<?php

namespace app\api\model;
use think\Db;
use think\Model;

class Article extends Model
{
    /**获取文章总数
     * @return int|string
     */
    public static function GetNum(){

        $result = Db::table('articles')->count('*');
        return $result;
    }

    /**获取文章列表
     * @param $page //页数
     * @param $num //每页显示行数
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function GetArticles($page,$num){
        $result = Db::table('articles')
            ->field('id,title,date,sourceName,sourceNum,url,thumbnails,type')
            ->order('id','desc')
            ->limit($page*10,$num)
            ->select();
        return $result;
    }

    /**根据文章id获取内容
     * @param $id
     * @return \PDOStatement
     */
    public static function GetArticleById($id){

        $result = Db::table('articles')
            ->where('id',$id)
            ->select();
        return $result;
    }

    /**保存用户反馈信息
     * @param $message
     * @param $number
     * @return bool 执行结果
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