<?php
namespace app\api\model;

use think\Db;
use think\Model;

class Comment extends Model
{

    public static function add($openId,$articleId,$content){
        $data = [
            'openid' => $openId,
            'articleid' => $articleId,
            'content' => $content
        ];
        $result = Db::table('comments')
            ->insert($data);

        return $result;
    }

    public static function show($articleId){
        $result = Db::table('comments')
            ->where('articleid',$articleId)
            ->order('time','desc')
            ->select();
        return $result;
    }
}
