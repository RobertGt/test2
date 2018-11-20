<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 18:50
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\UserServer;
use think\Request;

class User extends Base
{
    public function userList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new UserServer())->userList($param);

        ajax_info(0,'success', $response);
    }

    public function userDelete(Request $request)
    {
        $param = [
            'uid'  => $request->param('id',0, 'intval')
        ];

        $response = (new UserServer())->userDelete($param['uid']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }
}