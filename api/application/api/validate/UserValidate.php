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
        'mobile'      => 'length:11',
        'updateKey'   => 'require',
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
        'mobile.length'      =>  '请输入正确的手机号码',
        'updateKey.require'  =>  '修改失败，无权修改'
    ];

    protected $scene = [
        'register'       =>  ['email', 'password', 'code', 'imNumber'],
        'send'           =>  ['email'],
        'login'          =>  ['email', 'password' => 'require|min:6|checkUser'],
        'find'           =>  ['email', 'password', 'code'],
        'real'           =>  ['front', 'contrary', 'hand', 'realname'],
        'edit'           =>  ['password' => 'min:6|checkPass', 'imNumber', 'mobile'],
        'checkCode'      =>  ['code'],
        'emailUpdate'    =>  ['email', 'code', 'updateKey'],
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
            Cache::rm('find_' . $data['email']);
        }else{
            $sendCode = Cache::get('reg_' . $data['email']);
            Cache::rm('reg_' . $data['email']);
        }
        if($sendCode != $code){
            return "验证码不正确,请重新输入";
        }
        return true;
    }

    public function checkUser($password, $rule, $data)
    {
        $user = (new UserModel())->where(['email' => $data['email']])->field('uid, email, password, salt, wechat, imNumber, company, job, state, realname realState')->find()->toArray();
        if($user['password'] != md5($password . $user['salt'])){
            return "登录密码错误";
        }
        if($user['state'] == 1){
            return "你的账号已经被禁用";
        }
        $this->user = $user;
        return true;
    }

    public function checkPass($password, $rule, $data)
    {
        if(!$data['oldPassword']){
            return "请输入旧密码";
        }
        $user = (new UserModel())->where(['uid' => $data['uid']])->field('password, salt')->find()->toArray();
        if($user['password'] != md5($data['oldPassword'] . $user['salt'])){
            return "旧密码错误";
        }
        return true;
    }
}