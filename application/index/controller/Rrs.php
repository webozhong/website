<?php

namespace app\index\controller;
use app\index\model\Article;
use think\Controller;

class Rrs extends Controller {
    public function Index(){
        return $this->fetch('/rrss');
    }
    public function ArticleList(){

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $total = Article::GetTotal();
        $pageSize = 8;//每页显示记录数
        $totalPage = ceil($total/$pageSize);//总页数
        $startPage = $page*$pageSize;//开始记录数
        $arr['total'] = $total;
        $arr['pageSize'] = $pageSize;
        $arr['totalPage'] = $totalPage;
        $result2 = Article::GetArticlesByPage($page,$startPage,$pageSize);
        $js=array('jsonObj'=>$result2);
        $c=array_merge($js,$arr);
        return json($c);
    }

}