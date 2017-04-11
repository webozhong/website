<?php
namespace app\admin\controller;
use think\Controller;

class Base extends Controller {

    public $admin;
    public function _initialize(){
        //判断用户是否登录
        $isLogin = $this->isLogin();
        if (!$isLogin){
            return $this->redirect(url('/admin/login'));
        }
    }


    //判断是否登陆
    public function isLogin(){
        //获取session
        $user = $this->getLoginUser();
        if ($user){
            return true;
        }
        return false;
    }

    public function getLoginUser(){
        if(!$this->admin){
            $this->admin = session('admin','','admin');
        }
        return $this->admin;
    }

}