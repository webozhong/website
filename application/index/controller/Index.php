<?php
namespace app\index\controller;
use app\index\model\Article;
use think\Controller;
class Index extends Controller{
    public function Index()
    {
        //取出5篇文章
        $result = Article::GetArticles(5);
        $this->assign('result',$result);
        return $this->fetch('./index');
    }
}