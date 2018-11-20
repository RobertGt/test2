<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/18 23:50
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use app\admin\model\AdminModel;
use app\admin\server\AdminServer;
use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'aid'            => 'require',
        'account'        => 'require|max:10',
        'password'       => 'require|min:6',
        'newPassword'    => 'require|min:6|checkPassword',
        'repeatPassword' => 'require|min:6',
    ];

    protected $scene = [
        'login'        =>  ['account', 'password'],
        'reset'        =>  ['account', 'password', 'newPassword', 'repeatPassword'],
        'insert'       =>  ['account' => 'require|max:10|checkAccount', 'password'],
        'update'       =>  ['aid', 'account' => 'require|max:10|checkUpdateAccount', 'password' => 'min:6'],
        'checkId'      =>  ['aid'],
    ];

    public function checkPassword($newPassword, $rule, $data)
    {
        $checkPassword = (new AdminServer())->checkPassword($data['account'], $data['password']);

        if(!$checkPassword){
            return "原密码错误";
        }

        return true;
    }

    public function checkAccount($account, $rule, $data)
    {
        $admin = (new AdminModel())->where(['account' => $account])->field("aid")->find();
        if($admin){
            return "账号名已存在";
        }
        return true;
    }

    public function checkUpdateAccount($account, $rule, $data)
    {
        if($data['aid'] == 1){
            return true;
        }
        $admin = (new AdminModel())->where(['account' => $account, 'aid' => ['neq', $data['aid']]])->field("aid")->find();
        if($admin){
            return "账号名已存在";
        }
        return true;
    }
}