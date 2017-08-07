<?php
namespace app\admin\controller;
use app\admin\model\User;

class Users extends Base {
    public function Index(){
        //得到总记录数计算出总页数
        $total = User::GetTotal();
        $pageCount = ceil($total/10);
        $page = isset($_GET['page'])?$_GET['page']:'1';
        $result = User::ShowUsersByPage($page);
        foreach ($result as $item =>$value){
            if ($value['gender']=='1'){
                $result[$item]['gender']='男';
            }else if($value['gender']=='2') {
                $result[$item]['gender']='女';
            }else{
                $result[$item]['gender']='未知';
            }
        }
        $this->assign('page',$page);
        $this->assign('pageCount',$pageCount);
        $this->assign('result',$result);
        $this->assign('total',$total);
        return $this->fetch('/user-list');
    }
}