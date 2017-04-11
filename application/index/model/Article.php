<?php

namespace app\index\model;
use think\Db;
use think\Model;

class Article extends Model{


    /**
     * 返回最新的$num篇文章
     * @param $num 需要显示的文章数
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function GetArticles($num){
        $result = Db::table('articles')
            ->order('id','desc')
            ->limit(0,$num)
            ->select();
        return $result;
    }

    /**根据页面显示内容，每页10条
     * @param $page
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function GetArticlesByPage($page,$startPage,$pageSize){

        $result = Db::table('articles')
            ->field('id,title,date,sourceName,sourceNum,url,thumbnails,type')
            ->order('id','desc')
            ->limit($startPage,$pageSize)
            ->select();
        return $result;
    }

    public static function GetTotal()
    {
        $result = Db::table('articles')->count();
        return $result;
    }

}