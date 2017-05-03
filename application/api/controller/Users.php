<?php
namespace app\api\controller;

use app\api\model\User;
use think\Controller;

class Users extends Controller {

    /**
     * 保存用户信息接口
     * @return int|string
     */
    public function SaveUserInfo(){
        $openId = $_POST['openid'];
        $userName = $_POST['username'];
        $city = $_POST['city'];
        $sex = $_POST['sex'];

        if(!User::isExist($openId)){
            $result = User::addUser($openId,$userName,$city,$sex);
            return $result;
        }
    }

    /**
     *
     * 获取单个用户收藏列表接口
     */
    public function CollectionList(){
        $openId = $_POST['openid'];
        $result = User::getUserCollectionList($openId);
        return $result;
    }

    /**
     * 用户是否收藏某篇文章接口
     */
    public function IsCollection(){
        $openId = $_POST['openid'];
        $articleId = $_POST['articleid'];
        $result = User::collectionStatus($openId,$articleId);
        return $result;
    }

    /**
     * 保存用户收藏记录接口
     */
    public function SaveUserCollection(){
        $openId = $_POST['openid'];
        $articleId = $_POST['articleid'];
        $result = User::saveCollectionRecord($openId,$articleId);
        return $result;
    }

    /**
     * 单篇文章总收藏数接口
     */
    public function CollectionNum(){
        $articleId = $_POST['articleid'];
        $result = User::articleCollectionCount($articleId);
        return $result;
    }
    /**
     * 删除用户收藏记录接口
     */
    public function delUserCollectionNum(){
        $openId = $_POST['openid'];
        $articleId = $_POST['articleid'];
        $result = User::delCollectionRecord($openId,$articleId);
        return $result;
    }
}