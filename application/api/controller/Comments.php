<?php

namespace app\api\controller;
use app\api\model\Comment;
use app\api\model\User;
use think\Controller;
use think\Request;

class Comments extends Controller {

    public function add(Request $request){
        $openId = $request->post('openId');
        $articleId = $request->post('articleId');
        $content = $request->param('content');
        if ($openId==null||$articleId==null||$content==null) {
          return "缺少关键参数";
        }else{
          $result = Comment::add($openId,$articleId,$content);
          return $result;
        }
    }
    public function show(Request $request){
        $articleId = $request->get('articleid');
        $comments = Comment::show($articleId);
        if (!$comments) {
          return 'No data';
        }else {
          //return $comments;
          foreach ($comments as $key=>$item) {
            $userInfo = User::getUserByOpenid($item['openid']);
            $comments[$key]['userInfo']=$userInfo;
          }
          return $comments;
        }
    }
}
