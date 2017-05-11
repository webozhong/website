<?php
namespace app\api\controller;

use app\api\model\User;
use think\Controller;
use think\Session;

class Users extends Controller {


    /**
<<<<<<< HEAD
     * 小程序用户登录 换取openid session_key
=======
     * 小程序用户登录 openid session_key
>>>>>>> origin/master
     */
    public function Login(){
        //用户code
        $code = $_GET['code'];
        //小程序ID
        $appId = 'wx71342e901702563e';
        //小程序secret
        $secret = 'df90142f1e892876bafeef2aeccba876';
        //openid请求接口地址
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$secret&js_code=$code&grant_type=authorization_code";
        $openid = file_get_contents($url);
        return $openid;
<<<<<<< HEAD
    }

=======

    }
    public function Info(){
        phpinfo();
    }
>>>>>>> origin/master
    /**
     * 保存用户信息接口
     * @return int|string
     */
<<<<<<< HEAD
    public function SaveUserInfo(){
        $openid = $_POST['openid'];
        $avatarUrl = $_POST['avatarUrl'];
        $city = $_POST['city'];
        $country = $_POST['country'];
        $province = $_POST['province'];
        $nickName = $_POST['nickName'];
        $gender = $_POST['gender']; //性别 0：未知、1：男、2：女

        if(!User::isExist($openid)) {
            $result = User::addUser($openid,$avatarUrl,$city,$country,$province,$nickName,$gender);
            return $result;
        }else{
            $dateTime = date('Y-m-d H:i:s');
            $result = User::updateUser($openid,$avatarUrl,$city,$country,$province,$nickName,$gender,$dateTime);
=======
    private function SaveUserInfo($openId){
        if(!User::isExist($openId)){
            $result = User::addUser($openId);
>>>>>>> origin/master
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
        foreach ($result as $item=>$value){
            $v = substr($value['thumbnails'],0,19);
            $result["$item"]["thumbnails"]=$v;
        }
        return $result;
    }

    /**
     * 用户文章收藏状态接口
     */
    public function IsCollection(){
        $openId = $_POST['openid'];
        $articleId = $_POST['articleid'];
        $articleId = intval($articleId);
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
    public function DelUserCollection(){
        $openId = $_POST['openid'];
        $articleId = $_POST['articleid'];
        $result = User::delCollectionRecord($openId,$articleId);
        return $result;
    }
}