<?php

namespace app\api\model;

use think\Db;
use think\Model;

class User extends Model
{

    /**
     * 新增用户信息
     * @param $openid
     * @param $avatarUrl
     * @param $city
     * @param $country
     * @param $province
     * @param $nickName
     * @param $gender
     * @return int|string
     */
    public static function addUser($openid, $avatarUrl, $city, $country, $province, $nickName, $gender)
    {
        $data = [
            'openid' => $openid,
            'avatarUrl' => $avatarUrl,
            'city' => $city,
            'country' => $country,
            'province' => $province,
            'nickName' => $nickName,
            'gender' => $gender,
        ];
        $result = Db::table('users')
            ->insert($data);
        return $result;
    }

    /**判断用户是否存在
     * @param $openId
     * @return int|string
     */
    public static function isExist($openId)
    {
        $result = Db::table('users')
            ->where('openid', $openId)
            ->count();
        return $result;
    }

    /**
     * 判断用户是否更新信息
     * @param $openid
     * @param $avatarUrl
     * @param $city
     * @param $country
     * @param $province
     * @param $nickName
     * @param $gender
     * @return bool
     */
    public static function userInfoSame($openid, $avatarUrl, $city, $country, $province, $nickName, $gender)
    {
        $userInfo = Db::table('users')
            ->where('openid', $openid)
            ->select();
        $userInfo = $userInfo[0];
        $newUserInfo = [
            'openid' => $openid,
            'avatarUrl' => $avatarUrl,
            'city' => $city,
            'country' => $country,
            'province' => $province,
            'nickName' => $nickName,
            'gender' => $gender
        ];
        array_pop($userInfo);
        //比较数据
        if ($userInfo == $newUserInfo) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 更新用户信息
     * @param $openId
     * @param $avatarUrl
     * @param $city
     * @param $country
     * @param $province
     * @param $nickName
     * @param $gender
     * @return int|string
     */
    public static function updateUser($openId, $avatarUrl, $city, $country, $province, $nickName, $gender)
    {
        $data = [
            'avatarUrl' => $avatarUrl,
            'city' => $city,
            'country' => $country,
            'province' => $province,
            'nickName' => $nickName,
            'gender' => $gender,
        ];
        $result = Db::table('users')
            ->where('openid', $openId)
            ->update($data);
        return $result;
    }

    /**根据用户openid返回用户收藏列表
     * @param $openId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getUserCollectionList($openId)
    {
        $artList = Db::table('collections')
            ->where('openid', $openId)
            ->field('id,articleid')
            ->order('id', 'desc')
            ->select();
        if ($artList) {
            //构建sql语句
            $sql = "select id,title,thumbnails from articles where id in(";
            foreach ($artList as $item) {
                $value = $item['articleid'];
                $sql .= "$value,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ")order by field(id,";
            foreach ($artList as $item) {
                $value = $item['articleid'];
                $sql .= "$value,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ")";
            $result = Db::query($sql);
            return $result;
        }
        return $artList;

    }

    /**
     * 根据用户openid和文章id返回用户收藏该文章的状态
     * @param $openId
     * @param $articleId
     * @return int|string
     */
    public static function collectionStatus($openId, $articleId)
    {
        $result = Db::table('collections')
            ->where('openid', $openId)
            ->where('articleid', $articleId)
            ->count();
        return $result;
    }

    /**
     * 保存用户收藏记录返回执行结果
     * @param $openId
     * @param $articleId
     * @return int|string
     */
    public static function saveCollectionRecord($openId, $articleId)
    {
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
    public static function articleCollectionCount($articleId)
    {
        $result = Db::table('collections')
            ->where('articleid', $articleId)
            ->count();
        return $result;
    }

    /**
     * 删除用户收藏记录
     * @param $openId
     * @param $articleId
     * @return int
     */
    public static function delCollectionRecord($openId, $articleId)
    {
        $result = Db::table('collections')
            ->where('openid', $openId)
            ->where('articleid', $articleId)
            ->delete();
        return $result;
    }

    /**
     * 根据openid返回用户信息
     * @param $openId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getUserByOpenid($openId)
    {
        $result = Db::table('users')
            ->where('openid', $openId)
            ->select();
        return $result;
    }

    /**
     * 更新问卷状态
     * @param $openid
     * @return int|string
     */
    public static function updateStatus($openid){
        $data = [
            'status' => 1
        ];
        $result = Db::table('users')
            ->where('openid', $openid)
            ->update($data);
        return $result;
    }
}