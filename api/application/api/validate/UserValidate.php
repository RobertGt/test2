<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 22:51
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use app\api\model\UserModel;
use think\Cache;
use think\Validate;

class UserValidate extends Validate
{
    public $user = [];
    protected $rule = [
        'email'       => 'require|checkEmail',
        'password'    => 'require|min:6',
		'code'        => 'require|length:6|checkCode',
		'imNumber'    => 'number'
    ];

    protected $message  =   [
        "email.require"      =>  '邮箱号不能为空',
        "password.require"   =>  '密码不能为空',
        "password.min"       =>  '请输入不少于6位字符的密码',
        "code.require"       =>  '请输入验证码',
        "code.length"        =>  '验证码为6位字符',
        'imNumber.number'    =>  '请输入正确的QQ号'
    ];

    protected $scene = [
        'register'       =>  ['email', 'password', 'code', 'imNumber'],
        'send'           =>  ['email'],
        'login'          =>  ['email', 'password' => 'require|min:6|checkUser']
    ];

    public function checkEmail($email, $rule, $data)
    {
        if(!_checkemail($email)){
            return "请填写正确的邮箱号";
        }

        $count = (new UserModel())->where(['email' => $email])->count();

        if(!empty($data['find'])){
            if($count <= 0){
                return "邮箱已经还未注册";
            }
        }else{
            if($count > 0){
                return "邮箱已经被注册过";
            }
        }
        return true;
    }

    public function checkCode($code, $rule, $data)
    {
        if(!empty($data['find'])){
            $sendCode = Cache::get('find_' . $data['email']);
        }else{
            $sendCode = Cache::get('reg_' . $data['email']);
        }
        if($sendCode != $code){
            return "验证码不正确,请重新输入";
        }
        return true;
    }

    public function checkUser($password, $rule, $data)
    {
        $user = (new UserModel())->where(['email' => $data['email']])->field('uid, email, password, salt, wechat, imNumber, company, job')->find()->toArray();
        if($user['password'] != md5($password . $user['salt'])){
            return "登录密码错误";
        }
        $this->user = $user;
        return true;
    }
}