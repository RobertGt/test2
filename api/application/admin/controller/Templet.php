<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 1:52
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\TempletServer;
use app\admin\validate\ApplicationValidate;
use think\Request;

class Templet extends Base
{
    public function templetList(Request $request)
    {
        $param = [
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval')
        ];

        $response = (new TempletServer())->templetList($param);

        ajax_info(0,'success', $response);
    }

    public function templetUpdateState(Request $request)
    {
        $param = [
            'id'     => $request->param('id',0, 'intval'),
            'state'  => $request->param('state', '') ? 1 : 0,
        ];

        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new TempletServer())->templetUpdateState($param['id'], $param['state']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function templetInfo(Request $request)
    {
        $param = [
            'id'     => $request->param('id',0, 'intval')
        ];

        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new TempletServer())->templetInfo($param['id']);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function templetUpdate(Request $request)
    {
        $param = [
            'id'      => $request->param('id',0, 'intval'),
            'message' => $request->param('message','')
        ];

        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new TempletServer())->templetUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}