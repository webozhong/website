<?php
namespace app\api\model;
use think\Db;
use think\Model;

class ArticleAccess extends Model
{

    public static function Add($openId,$articleId,$duration){

        $data = [
            'openid' =>$openId,
            'articleid' => $articleId,
            'duration' => $duration
        ];
        $result = Db::table('article_access')
            ->insert($data);
        return $result;
    }

}