<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/6 0:34
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\ApplicationServer;
use app\api\validate\ApplicationValidate;
use think\Request;

class App extends Base
{
    public function appList(Request $request)
    {
        $param = [
            'appName'  => $request->param('appName', ''),
            'platform' => $request->param('platform', ''),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];
        $response = (new ApplicationServer())->appList($param, $this->userInfo['uid']);

        ajax_info(0,'success', $response);
    }

    public function appDelete(Request $request)
    {
        $param = [
            'appId'    => authcode($request->param('appId', ''))
        ];
        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new ApplicationServer())->appDelete($param['appId'], $this->userInfo['uid']);
        if($response){
            ajax_info(0, 'success');
        }else{
            ajax_info(1, '删除失败');
        }
    }

    public function appInfo(Request $request)
    {
        $param = [
            'appId'    => authcode($request->param('appId', ''))
        ];
        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new ApplicationServer())->appInfo($param['appId'], $this->userInfo['uid']);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function appVersionRemark(Request $request)
    {
        $param = [
            'appId'    => authcode($request->param('apkId', '')),
            'remark'   => $request->param('remark', '')
        ];
        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new ApplicationServer())->appVersionRemark($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}