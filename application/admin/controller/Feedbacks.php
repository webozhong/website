<?php

namespace app\admin\controller;
use app\admin\model\Feedback;

class Feedbacks extends Base {
    public function Index(){
        $total = Feedback::GetTotal();
        $pageCount = ceil($total/10);
        $page = isset($_GET['page'])?$_GET['page']:'1';
        $result = Feedback::ShowFeedbackByPage($page);

        $this->assign('page',$page);
        $this->assign('pageCount',$pageCount);
        $this->assign('result',$result);
        return $this->fetch('/user-feedback');
    }
}