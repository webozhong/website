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
        return $this->fetch('/user-list');
    }

    /**
     * 用户留存率
     * @return string
     */
    public function retention(){
        if(isset($_GET['interval'])){
            $interval= $_GET['interval'];
            if ($interval == 1){
                $cycle = 'day';
                $k = 7;
            }
            elseif ($interval == 2){
                $cycle = 'week';
                $k = 5;
            }
            elseif ($interval == 3){
                $cycle = 'month';
                $k = 12;
            }else{
                return '参数无效';
            }
            $newUsersArray = [];
            $newUsersCountArray = [];
            for($i = 1;$i<=$k;$i++){
                $startTime = date("Y-m-d",strtotime("-$i $cycle"));
                $end = $i-1;
                $endTime = date("Y-m-d",strtotime("-$end $cycle"));
                $newUsers = User::getNewUsers($startTime,$endTime);
                $newUsersCount = count($newUsers);
                array_push($newUsersArray,$newUsers);
                //每天的新用户计数数组
                array_push($newUsersCountArray,$newUsersCount);
            }
            //$activeUserArray = [];
            $activeUsersCountArray = [];
            //$item每一天的新用户列表
            foreach ($newUsersArray as $list=>$item){
                //$value当天的每个新用户
                $activeUsers=[];
                foreach ($item as $key=>$value){
                    //$activeUsers 当天使用过小程序的用户
                    $start = $key+1;
                    $startTime = date("Y-m-d",strtotime("-$start $cycle"));
                    $endTime = date("Y-m-d",strtotime("-$key $cycle"));
                    $activeUser = User::getActiveUsers($value['openid'],$startTime,$endTime);
                    if (!empty($activeUser)){
                        //array_push($activeUserArray,$activeUser);
                        array_push($activeUsers,$activeUser);
                    }
                }
                $activeUsersCount = (count($activeUsers));
                array_push($activeUsersCountArray,$activeUsersCount);
            }
            //echo count($activeUserArray);
            var_dump($newUsersCountArray,$activeUsersCountArray);
            $this->assign('newUsersCountArray',$newUsersCountArray);
            $this->assign('activeUsersCountArray',$activeUsersCountArray);
            return $this->fetch('/user-access');
        }
    }
}