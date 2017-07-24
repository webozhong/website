<?php
namespace app\api\controller;
use app\api\model\AppAccess;
use app\api\model\ArticleAccess;
use think\Controller;

class Statistics extends Controller{
    public function app(){
        $openId = $_POST['openId'];
        $equipment = $_POST['equipment'];
        $result = AppAccess::Add($openId,$equipment);
        return $result;
    }

    public function article(){
        $openId = $_POST['openId'];
        $articleId = $_POST['articleId'];
        $duration = $_POST['duration'];
        $duration = floor($duration/1000);
        if($duration<=600&&$duration>=10){
            $result = ArticleAccess::Add($openId,$articleId,$duration);
            return $result;
        }
    }
}