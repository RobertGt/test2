<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 23:01
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\UserModel;
use think\Config;
use think\Log;

class LoginServer
{

    public function login($param)
    {
		$create['nickname'] = $param['nickname'];
		$create['avatar'] = $param['avatar'];
		$create['sex'] = (int)$param['sex'];
		$create['province'] = $param['province'];
		$create['city'] = $param['city'];
		$create['platform'] = $param['loginType'];
		$create['openId'] = $param['openid'];
		$userid = (new UserModel())->register($create, $param['imei']);
		if($userid){
			$create['uid'] = authcode($userid, 'ENCODE');
			return $create;
		}else{
			return false;
		}
    }
}