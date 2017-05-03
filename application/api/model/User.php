<?php
namespace app\api\model;
use think\Db;
use think\Model;

class User extends Model{

    /**添加用户信息
     * @param $openId
     * @param $userName
     * @param $city
     * @param $sex
     * @return int|string
     */
    public static function addUser($openId,$userName,$city,$sex){
        $data = [
            'openid'=>$openId,
            'username'=>$userName,
            'city'=>$city,
            'sex'=>$sex,
        ];
        $result = Db::table('users')->insert($data);
        return $result;
    }

    /**判断用户是否存在
     * @param $openId
     * @return int|string
     */
    public static function isExist($openId){
        $result  = Db::table('users')
            ->where('openid',$openId)
            ->count();
        return $result;
    }

    /**根据用户openid返回用户收藏列表
     * @param $openId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getUserCollectionList($openId){
        $result = Db::table('collections')
            ->where('openid',$openId)
            ->select();
        return $result;
    }

    /**
     * 根据用户openid和文章id返回用户收藏该文章的状态
     * @param $openId
     * @param $articleId
     * @return int|string
     */
    public static function collectionStatus($openId,$articleId){
        $result = Db::table('collections')
            ->where(['openid','articleid'],[$openId,$articleId])
            ->count();
        return $result;
    }

    /**
     * 保存用户收藏记录返回执行结果
     * @param $openId
     * @param $articleId
     * @return int|string
     */
    public static function saveCollectionRecord($openId,$articleId){
        $data = [
            'openid' => $openId,
            'articleid' => $articleId,
        ];
        $result = Db::table('collections')
            ->insert($data);
        return $result;
    }

    /**
     * 根据文章id返回该文章的收藏总数
     * @param $articleId
     * @return int|string
     */
    public static function articleCollectionCount($articleId){
        $result = Db::table('collections')
            ->where('articleid',$articleId)
            ->count();
        return $result;
    }

    public static function delCollectionRecord($openId,$articleId){
        $result = Db::table('collections')
            ->where(['openid','articleid'],[$openId,$articleId])
            ->delete();
        return $result;
    }
}