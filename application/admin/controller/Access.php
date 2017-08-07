<?php

namespace app\admin\controller;
use app\admin\model\AppAccess;
use app\admin\model\ArticleAccess;
use app\admin\model\User;
use think\Request;

class Access extends Base
{
    /**
     * 用户留存率
     * @return string
     */
    public function index(Request $request){
        $type = $request->get('type')==null?1:$request->get('type');
        $interval = $request->get('interval')==null?1:$request->get('interval');
        if ($type==1){
            if ($interval == 1){
                $cycle = 'day';
                $k = 12;
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
            $activeUsersCountArray = [];
            //$item每一天的新用户列表
            foreach ($newUsersArray as $list=>$item){
                //$value当天的每个新用户
                $activeUsers=[];
                foreach ($item as $key=>$value){
                    $activeUser = null;
                    //$activeUsers 当天使用过小程序的用户
                    $start = $key+1;
                    $startTime = date("Y-m-d",strtotime("-$start $cycle"));
                    $endTime = date("Y-m-d",strtotime("-$key $cycle"));
                    $activeUser = AppAccess::getActiveUsers($value['openid'],$startTime,$endTime);
                    if (!empty($activeUser)){
                        array_push($activeUsers,$activeUser);
                    }
                }
                print_r($activeUsers);
                $activeUsersCount = (count($activeUsers));
                array_push($activeUsersCountArray,$activeUsersCount);

            }
            $retentionArray = [];
            foreach ($newUsersCountArray as $key=>$value) {
                $dividend = $activeUsersCountArray[$key];
                if ($value==0){
                    array_push($retentionArray,'0%');
                }else{
                    $result =  round($dividend/$value,5)."%";
                    array_push($retentionArray,$result);
                }
            }

            var_dump($newUsersCountArray,$activeUsersCountArray,$retentionArray);
            $this->assign('newUsersCountArray',$newUsersCountArray);
            $this->assign('activeUsersCountArray',$activeUsersCountArray);
            $this->assign('retentionArray',$retentionArray);
            return $this->fetch('/user-access');
        }
        elseif ($type==2){
            //老用户留存率
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
                $endTime = date("Y-m-d",strtotime("-$i $cycle"));
//                $end = $i-1;
//                $endTime = date("Y-m-d",strtotime("-$end $cycle"));
                $newUsers = User::getOldUsers($endTime);
                $newUsersCount = count($newUsers);
                array_push($newUsersArray,$newUsers);
                //每天的新用户计数数组
                array_push($newUsersCountArray,$newUsersCount);
            }
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
                    $activeUser = AppAccess::getActiveUsers($value['openid'],$startTime,$endTime);

                    if (!empty($activeUser)){
                        array_push($activeUsers,$activeUser);
                    }
                }
                $activeUsersCount = (count($activeUsers));
                array_push($activeUsersCountArray,$activeUsersCount);
            }
            $retentionArray = [];
            foreach ($newUsersCountArray as $key=>$value) {
                $dividend = $activeUsersCountArray[$key];
                if ($value==0){
                    array_push($retentionArray,'0%');
                }else{
                    $result =  round($dividend/$value,5)."%";
                    array_push($retentionArray,$result);
                }
            }
            var_dump($newUsersCountArray,$activeUsersCountArray,$retentionArray);
            //用户留存率
            $this->assign('newUsersCountArray',$newUsersCountArray);
            $this->assign('activeUsersCountArray',$activeUsersCountArray);
            $this->assign('retentionArray',$retentionArray);
            return $this->fetch('/user-access');
        }
    }

    public function pv(Request $request){
        $type = $request->get('type')==null?1:$request->get('type');
        $interval = $request->get('interval')==null?1:$request->get('interval');
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
            $newUsers = ArticleAccess::userPv($startTime,$endTime);
            var_dump($newUsers);
            $newUsersCount = count($newUsers);
            array_push($newUsersArray,$newUsers);
            //每天的新用户计数数组
            array_push($newUsersCountArray,$newUsersCount);
        }
        var_dump($newUsersCountArray);
    }
}