<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/12 23:27
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\AdminServer;
use think\Request;

class Base
{
    protected $adminInfo = [];

    public function __construct()
    {
        $this->checkToken();
    }

    /**
     * 检查Token
     */
    private function checkToken()
    {
        $request = Request::instance();

        $token     = $request->header('token', '');

        $admin = (new AdminServer())->adminInfo(0, $token);

        if (!$admin){
            ajax_info(401, 'failure of authentication');
        }

        $this->adminInfo = $admin;

        return true;
    }
}