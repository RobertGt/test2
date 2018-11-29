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
		'imNumber'    => 'number',
        'front'       => 'require',
        'contrary'    => 'require',
        'hand'        => 'require',
        'realname'    => 'in:0,2',
    ];

    protected $message  =   [
        "email.require"      =>  '邮箱号不能为空',
        "password.require"   =>  '密码不能为空',
        "password.min"       =>  '请输入不少于6位字符的密码',
        "code.require"       =>  '请输入验证码',
        "code.length"        =>  '验证码为6位字符',
        'imNumber.number'    =>  '请输入正确的QQ号',
        'front.require'      =>  '请上传身份证正面',
        'contrary.require'   =>  '请上传身份证反面',
        'hand.require'       =>  '请上传手持身份证',
        'realname.in'        =>  '你的账号已经审核通过,无限重复验证',
    ];

    protected $scene = [
        'register'       =>  ['email', 'password', 'code', 'imNumber'],
        'send'           =>  ['email'],
        'login'          =>  ['email', 'password' => 'require|min:6|checkUser'],
        'find'           =>  ['email', 'password', 'code'],
        'real'           =>  ['front', 'contrary', 'hand', 'realname']
    ];

    public function checkEmail($email, $rule, $data)
    {
        if(!_checkemail($email)){
            return "请填写正确的邮箱号";
        }

        $count = (new UserModel())->where(['email' => $email])->count();

        if(!empty($data['find'])){
            if($count <= 0){
                return "邮箱还未注册";
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
        $user = (new UserModel())->where(['email' => $data['email']])->field('uid, email, password, salt, wechat, imNumber, company, job, state')->find()->toArray();
        if($user['password'] != md5($password . $user['salt'])){
            return "登录密码错误";
        }
        if($user['state'] == 1){
            return "你的账号已经被禁用";
        }
        $this->user = $user;
        return true;
    }
}