<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/29 23:46
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\UserServer;
use app\api\validate\UserValidate;
use think\Request;

class Login extends Base
{
    public function realName(Request $request)
    {
        $param = [
            'front'      => $request->param('front',''),
            'contrary'   => $request->param('contrary',''),
            'hand'       => $request->param('hand',''),
            'realname'   => $this->userInfo['realname'],
            'uid'        => $this->userInfo['uid'],
        ];
        $validate = new UserValidate();
        if(!$validate->scene('real')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->real($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'提交失败');
        }
    }
}