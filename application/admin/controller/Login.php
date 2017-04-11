<?php
namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{

    public function index(){

        return $this->fetch('/login');
    }

    public function login(){
        if(request()->isPost()){
            //获取相关数据
            $data = input('post.');
            $ret = model('Admin')->userLogin($data['username'],$data['password']);
            if(!$ret){
                $this->error('用户名或密码错误！');
            }

            //记录用户最后登陆
            //model('Admin')->updateById(['last_login_time => time()'],$ret->id);

            //保存用户信息
            session('admin',$ret,'admin');
            return $this->redirect('/admin/articles');

        }else{
            return $this->redirect('/admin/login');
        }
    }


    /*
     * 退出登陆
     */
    public function Logout(){
        //清除session
        session(null,'admin');

        //跳出
        $this->redirect('/admin/login');
    }
}