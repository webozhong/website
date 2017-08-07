<?php
namespace app\api\controller;
use app\api\model\User;
use think\Controller;
class Users extends Controller {


    /**
     * 小程序登录、解密、返回用户私密信息
     */
    public function Login(){

        //小程序APPID和AppSecret
        $APPID = 'wx71342e901702563e';
        $AppSecret = 'df90142f1e892876bafeef2aeccba876';

        //接收POST数据
        $code = $_POST['code'];//换取openId和session_key的code
        $rawData = $_POST['rawData'];
        $signature =$_POST['signature'];
        $encryptedData = $_POST['encryptedData'];
        $iv = $_POST['iv'];
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$APPID."&secret=".$AppSecret."&js_code=".$code."&grant_type=authorization_code";
        $arr = file_get_contents($url);
        $arr = json_decode($arr,true);
        $openid = $arr['openid'];
        $session_key = $arr['session_key'];

        // 数据签名校验
        $signature2 = sha1($rawData.$session_key);  //记住不应该用TP中的I方法，会过滤掉必要的数据
        if ($signature != $signature2) {
            return '数据签名验证失败！';
            die;
        }
        //开发者如需要获取敏感数据，需要对接口返回的加密数据( encryptedData )进行对称解密
        Vendor("wechat.wxBizDataCrypt");  //加载解密文件在vendor文件夹下
        $pc = new \WXBizDataCrypt($APPID, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);  //其中$data包含用户的所有数据
        if ($errCode == 0) {
            return $data;
        } else {
            print($errCode . "\n");
        }

        //生成第三方3rd_session
//        $session3rd  = null;
//        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
//        $max = strlen($strPol)-1;
//        for($i=0;$i<16;$i++){
//            $session3rd .=$strPol[rand(0,$max)];
//        }
//        echo $session3rd;
    }

    /**
     * 保存用户信息接口
     * @return int|string
     */

    public function SaveUserInfo(){
        $openid = $_POST['openid'];
        $avatarUrl = $_POST['avatarUrl'];
        $city = $_POST['city'];
        $country = $_POST['country'];
        $province = $_POST['province'];
        $nickName = $_POST['nickName'];
        $gender = $_POST['gender']; //性别 0：未知、1：男、2：女

        //如果从未登录，添加注册信息
        if(!User::isExist($openid)){
            $result = User::addUser($openid,$avatarUrl,$city,$country,$province,$nickName,$gender);
            return $result;
        }else{
            //检测用户是否更改了微信头像昵称等资料
            $isSame = User::userInfoSame($openid,$avatarUrl,$city,$country,$province,$nickName,$gender);

            if (!$isSame){
                $result = User::updateUser($openid, $avatarUrl, $city, $country, $province, $nickName, $gender);
                if($result){
                    return '用户资料更新成功';
                }
            }else{
                return '用户未更改资料,不更新数据';
            }
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
