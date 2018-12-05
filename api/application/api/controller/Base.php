<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/29 23:31
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\model\UserModel;
use think\Cache;
use think\Request;

class Base
{
    protected $userInfo = [];

    public function __construct()
    {
        $this->checkToken();
    }

    /**
     * 检查是否登录
     */
    private function checkToken()
    {
        $request = Request::instance();
        $token     = $request->header('token', '');
        if(!$token){
            ajax_info(401, 'failure of authentication');
        }
        //$userInfo = Cache::get($token);
        $field = "uid, email, state, realname, upload";
        $userInfo = (new UserModel())->where(['token' => $token])->field($field)->find();

        if (!$userInfo){
            ajax_info(401, 'failure of authentication');
        }
        $this->userInfo = $userInfo->getData();
        if($this->userInfo['state'] == 1){
            ajax_info(403, '你的账号已经被禁用');
        }
        return true;
    }
}