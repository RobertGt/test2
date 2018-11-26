<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 22:49
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\LoginServer;
use app\api\server\UserServer;
use app\api\validate\UserValidate;
use think\Request;

class User
{
    public function register(Request $request)
    {
        $param = [
            'email'      => $request->param('email',''),
            'password'   => $request->param('password',''),
            'code'       => $request->param('code',''),
			'wechat'     => $request->param('wechat',''),
			'imNumber'   => $request->param('imNumber', ''),
			'company'    => $request->param('company',''),
			'job'        => $request->param('job','')
        ];
        $validate = new UserValidate();
        if(!$validate->scene('register')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->register($param);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'注册失败');
        }
    }

    public function sendMail(Request $request)
    {
        $param = [
            'email'      => $request->param('email',''),
            'find'       => $request->param('find','') ? 1 : 0,
        ];
        $validate = new UserValidate();
        if(!$validate->scene('send')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->sendMail($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'发送失败');
        }
    }

    public function login(Request $request)
    {
        $param = [
            'email'      => $request->param('email',''),
            'password'   => $request->param('password',''),
            'find'       => 1
        ];
        $validate = new UserValidate();
        if(!$validate->scene('login')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->login($validate->user);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'登录失败');
        }
    }
}