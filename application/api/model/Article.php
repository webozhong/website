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

}