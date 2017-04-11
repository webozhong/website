<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Admin extends Model
{
    /*
     * 验证用户登陆
     * @param string $username 用户名
     * @param string $password 密码
     * @return bool true 登陆成功 false 登陆失败
     */
    public function userLogin($username,$password){

        $result = Db::table('admin')
            ->where(['username'=>$username,'password'=>md5($password)])
            ->find();
        if($result==true){
            return true;
        }else{
            return false;
        }
    }

    public function updateById($data,$id){

        //allowField 过滤data数组中非数据表中的数据
        return $this->allowField(true)->save($data,['id'=>$id]);
    }

}