<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 18:50
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\UserServer;
use app\admin\validate\UserValidate;
use think\Cache;
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

    public function userStateUpdate(Request $request)
    {
        $param = [
            'id'     => $request->param('id',0, 'intval'),
            'state'  => $request->param('state', '') ? 1 : 0,
        ];

        $validate = new UserValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new UserServer())->userStateUpdate($param['id'], $param['state']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function userRealnameUpdate(Request $request)
    {
        $param = [
            'id'        => $request->param('id', 0, 'intval'),
            'realname'  => $request->param('realname', 0, 'intval'),
        ];

        $validate = new UserValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new UserServer())->userRealnameUpdate($param['id'], $param['realname']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function userPasswordUpdate(Request $request)
    {
        $param = [
            'id'        => $request->param('id', 0, 'intval'),
            'password'  => $request->param('password', ''),
        ];

        $validate = new UserValidate();
        if(!$validate->scene('passwordUpdate')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->userPasswordUpdate($param['id'], $param['password']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function userInfo(Request $request)
    {
        $param = [
            'id'    => $request->param('id',0, 'intval')
        ];

        $validate = new UserValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new UserServer())->userInfo($param['id']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function userUpdate(Request $request)
    {
        $param = [
            'id'          => $request->param('id',0, 'intval'),
            'upload'      => $request->param('upload',0, 'intval'),
            'download'    => $request->param('download',0, 'intval'),
            'surplus'     => $request->param('surplus',0, 'intval')
        ];

        $validate = new UserValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new UserServer())->userUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function realName(Request $request)
    {
        $realName  = $request->param('realName') ? 1 : 0;
        Cache::set('realName', $realName);
        ajax_info(0,'success');
    }
}