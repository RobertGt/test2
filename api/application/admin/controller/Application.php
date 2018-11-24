<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 17:30
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\ApplicationServer;
use app\admin\validate\ApplicationValidate;
use think\Request;

class Application extends Base
{
    public function appListByUser(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
            'uid'      => $request->param('uid', 0,'intval'),
        ];

        $response = (new ApplicationServer())->appListByUser($param);

        ajax_info(0,'success', $response);
    }

    public function appList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval')
        ];

        $response = (new ApplicationServer())->appList($param);

        ajax_info(0,'success', $response);
    }

    public function appStateUpdate(Request $request)
    {
        $param = [
            'id'     => $request->param('id',0, 'intval'),
            'state'  => $request->param('state', '') ? 1 : 0,
        ];

        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ApplicationServer())->appStateUpdate($param['id'], $param['state']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function appInfo(Request $request)
    {
        $param = [
            'id'     => $request->param('id',0, 'intval')
        ];

        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ApplicationServer())->appInfo($param['id']);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function updateList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
            'appId'    => $request->param('appId', 0,'intval'),
        ];

        $response = (new ApplicationServer())->updateList($param);

        ajax_info(0,'success', $response);
    }

    public function downList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
            'appId'    => $request->param('appId', 0,'intval'),
        ];

        $response = (new ApplicationServer())->downList($param);

        ajax_info(0,'success', $response);
    }
}