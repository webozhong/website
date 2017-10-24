<?php
namespace app\api\controller;

use app\api\model\Questionnaire;
use app\api\model\User;
use think\Controller;
use think\Request;

use PHPMailer\PHPMailer\PHPMailer;

class Questionnaires extends Controller
{
    public function index(Request $request){
        $method = $request->method();
        if ($method=='POST'){
            return $this->create($request);
        }else if($method=='GET'){
            return $this->read($request);
//        }else if($method=='PATCH'){
//            return $this->update($request);
//        }else if($method=='DELETE'){
//            return $this->delete($request);
        }else {
            return '请求方式不支持';
        }
    }

    private function create($request){
        $param = $request->param();
        $keys = ['openid', 'q1', 'q2', 'q3', 'q4','q5','q6','q7','q8'];
        foreach ($keys as $key){
            if(!array_key_exists($key,$param)){
                return "缺少参数：$key";
            }
        }
        if (!$param['openid'] || $param['openid']=='undefined'){
            return '无效用户';
        }

        $data = [
            'openid' => $param['openid'],
            'q1' => $param['q1'],
            'q2' => $param['q2'],
            'q3' => $param['q3'],
            'q4' => $param['q4'],
            'q5' => $param['q5'],
            'q6' => $param['q6'],
            'q7' => $param['q7'],
            'q8' => $param['q8'],
        ];
        $result = Questionnaire::add($data);

        if($result == 1){

            //邮件模板
            function mailBody($data){
                $answer = [
                    'q1' => [
                        'A' => '扫描二维码',
                        'B' => '微信搜索',
                        'C' => '公众号关联',
                        'D' => '好友推荐',
                        'E' => '历史记录',
                    ],
                    'q2' => [
                        'A' => '一周前',
                        'B' => '一月前',
                        'C' => '三月前',
                        'D' => '半年前',
                        'E' => '很早就开始使用了',
                    ],
                    'q3' => [
                        'A' => '书法',
                        'B' => '绘画',
                        'C' => '都很喜欢',
                    ],
                    'q4' => [
                        'A' => '90年代',
                        'B' => '80年代',
                        'C' => '70年代',
                        'D' => '60年代',
                        'E' => '50年代',
                        'F' => '其他'
                    ],
                    'q5' => [
                        'A' => '太慢了，我希望能获得更多内容',
                        'B' => '正好合适，非常适合我',
                        'C' => '太快了，内容太多看不过来',
                        'D' => '无所谓快慢',
                    ],
                    'q6' => [
                        'A' => '愿意',
                        'B' => '不愿意',
                        'C' => '愿意，但应该付给我稿费',
                    ]
                ];

                $mailBody = '<pre>';
                $mailBody .= '<h2>用户体验问卷调查报告</h2>';
                $mailBody .= '<h4>1. 您是从什么渠道得知遇见书画的？</h4>';
                $mailBody .= '<p>'.$answer['q1'][$data['q1']].'</p>';
                $mailBody .= '<h4>2. 您第一次使用遇见书画是在什么时候？</h4>';
                $mailBody .= '<p>'.$answer['q2'][$data['q2']].'</p>';
                $mailBody .= '<h4>3. 书法和绘画您更偏爱哪个？</h4>';
                $mailBody .= '<p>'.$answer['q3'][$data['q3']].'</p>';
                $mailBody .= '<h4>4. 您的出生年代？</h4>';
                $mailBody .= '<p>'.$answer['q4'][$data['q4']].'</p>';
                $mailBody .= '<h4>5. 您认为遇见书画的内容更新速度如何？</h4>';
                $mailBody .= '<p>'.$answer['q5'][$data['q5']].'</p>';
                $mailBody .= '<h4>6. 如果以不付费的形式在遇见书画中展示您的作品，您是否愿意？（我们和您双方都无需支付费用）</h4>';
                $mailBody .= '<p>'.$answer['q6'][$data['q6']].'</p>';
                $mailBody .= '<h4>7. 除了遇见书画，您还在使用的其他书法类小程序或公众号有哪些？*</h4>';
                $mailBody .= '<p>'.$data['q7'].'</p>';
                $mailBody .= '<h4>8. 请说说您觉得遇见书画做得不好地方，我们希望得到您的批评并以此改进？*</h4>';
                $mailBody .= '<p>'.$data['q8'].'</p>';
                $mailBody .= '</pre>';

                return $mailBody;
            }

            //数据写入数据库后将模板发送到企业邮箱
            $mail = new PHPMailer();

            $mail->isSMTP(); //使用 SMTP 服务
            $mail->CharSet = 'utf8'; //编码为utf8，不设置中文会乱码
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = "smtp.exmail.qq.com"; //发送方 SMTP 服务器地址
            $mail->Port = 465; //端口
            $mail->SMTPAuth = true; //是否使用身份验证
            $mail->SMTPSecure = 'ssl'; //使用ssl协议
            $mail->Username = "admin@webozhong.com"; //发送方用户名
            $mail->Password = "w7H2t6Y3"; //发送方密码
            $mail->setFrom('admin@webozhong.com', 'admin'); //设置发件人信息
            $mail->addAddress('all@webozhong.com'); //设置收件人信息

            $mail->Subject = '收到一封新的用户调查问卷'.date("Y/m/d"); //邮件标题
            $mail->Body = mailBody($data);
            $mail->AltBody = '这是一封问卷发送功能的测试邮件，请忽略！';

            if (!$mail->send()) {
                return "Mailer Error: " . $mail->ErrorInfo;
            } else {
                return "Message sent!";
            }
        }
        return $result;
    }

    private function read($request){
        $param = $request->param();
        if (empty($param['openid'])){
            return $request;
        }
        if(array_key_exists('openid',$param)){
            $user = User::getUserByOpenid($param['openid']);

            //验证用户是否拒绝或已填写问卷
            if($user[0]['status'] != 0 || Questionnaire::isFill($param['openid'])){
                return true;
            }else{
                return false;
            }
        }
    }

    private function update($request){

    }

    private function delete($request){

    }

}