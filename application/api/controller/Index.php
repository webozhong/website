<?php
namespace app\api\controller;

use app\api\model\Article;
use think\Controller;

class Index extends Controller
{
    /**首页内容接口
     * @return array
     */
    public function Index()
    {
        $page=$_GET['page'];
        $total = Article::GetNum();
        $totalPage = ceil($total/10);//总页数
        $arr['total'] = $total;
        $arr['totalPage'] = $totalPage;
        $arr['page'] = $page;
        $result = Article::GetArticles($page,10);
        $js=array('jsonObj'=>$result);
        $c=array_merge($js,$arr);
        return $c;
    }

    /**
     * 文章内容接口
     * @return \PDOStatement
     */
    public function GetArticleInfo(){
        $id = $_GET['id'];
        $result = Article::GetArticleById($id);
        return $result;
    }

    /**
     * 用户反馈保存接口
     */
    public function SaveFeedback(){
        $data = $_POST;
        if(!isset($data['message']) && !isset($data['number'])){
            return false;
        };
        $result = Article::SaveFeedback($data['message'],$data['number']);
        return $result;
    }
    public function Test(){
        //$id = $_GET['id'];
        //$result = Article::Test($id);
        $url = 'http://mp.weixin.qq.com/s/Mg_lZWh1vl9vP3dW830BPQ';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //装载html
        $content = curl_exec($ch);

        //删除js
        $search ="'<script[^>]*?>.*?</script>'si";
        $replace = "";
        $content = preg_replace($search,$replace,$content);

        //删除head
        $search ="'<head[^>]*?>.*?</head>'si";
        $content = preg_replace($search,$replace,$content);

        //删除标题信息
        $search ="'<h2 class=\"rich_media_title\"[^>]*?>.*?</h2>'si";
        $content = preg_replace($search,$replace,$content);
        //删除来源信息
        $search ="'<div class=\"rich_media_meta_list\"[^>]*?>.*?</div>'si";
        $content = preg_replace($search,$replace,$content);

        //删除防盗链
        $content = str_replace('data-src','src',$content);

        //删除&nbsp; <!DOCTYPE html> -->
        $content = str_replace('&nbsp;','',$content);
        $content = str_replace('<!DOCTYPE html>','',$content);
        //$content = str_replace('-->','',$content);
        //删除留言板块
        $a = (strpos($content,'div class="ct_mpda_wrp"'));
        $b = (strpos($content,'<div/>'));
        $remove = substr($content,$a-1,$b-2);
        $content = str_replace($remove,"</html>",$content);
        return $content;
    }
}
