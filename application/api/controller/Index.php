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
}
