<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 22:49
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\LoginServer;
use app\api\validate\UserValidate;
use think\Request;

class User extends Base
{
    public function login(Request $request)
    {
        $param = [
            'loginType'  => $request->param('loginType',''),
            'imei'       => $request->param('imei',''),
            'openid'     => $request->param('userId',''),
			'avatar'     => $request->param('avatar',''),
			'sex'        => $request->param('sex', 0, 'intval'),
			'province'   => $request->param('province',''),
			'city'       => $request->param('city',''),
			'nickname'   => $request->param('nickname','')
        ];
        $validate = new UserValidate();
        if(!$validate->scene('login')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new LoginServer())->login($param);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'登录失败');
        }
    }
}