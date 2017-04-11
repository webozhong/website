<?php
namespace app\api\controller;

use app\api\model\Article;
use think\Controller;

class Index extends Controller
{
    public function Index()
    {
        $page=$_GET['page'];
        $total = Article::GetNum();
        $totalPage = ceil($total/10);//总页数
        $arr['total'] = $total;
        $arr['totalPage'] = $totalPage;
        $result = Article::GetArticles($page,10);
        $js=array('jsonObj'=>$result);
        $c=array_merge($js,$arr);
        return $c;
    }
    public function GetBanner(){
        $result = Article::GetArticles(0,3);
        return $result;
    }

    public function GetArticleInfo(){
        $id = $_GET['id'];
        $result = Article::GetArticleById($id);
        return $result;
    }
    
}
