<?php
namespace app\api\controller;

use app\api\model\User;
use think\Controller;

class Users extends Controller {


    /**
     * 小程序用户登录 openid session_key
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

    }
    public function Info(){
        phpinfo();
    }
    /**
     * 保存用户信息接口
     * @return int|string
     */
    private function SaveUserInfo($openId){
        if(!User::isExist($openId)){
            $result = User::addUser($openId);
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