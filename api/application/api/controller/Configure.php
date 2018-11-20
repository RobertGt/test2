<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/13 0:06
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\ConfigureServer;
use app\api\validate\ConfigureValidate;
use app\api\validate\StatisticValidate;
use think\Request;

class Configure extends Base
{
    public function setting(Request $request)
    {
        $param = [
            'imei'        => $request->param('imei',''),
            'theme'       => $request->param('theme',0, 'intval'),
            'taskFinish'  => $request->param('taskFinish',0, 'intval'),
            'restFinish'  => $request->param('restFinish',0, 'intval'),
            'noise'       => $request->param('noise',0, 'intval'),
            'taskTime'    => $request->param('taskTime',25, 'intval'),
            'sortRest'    => $request->param('sortRest',5, 'intval'),
            'longRest'    => $request->param('longRest',15, 'intval'),
            'taskNum'     => $request->param('taskNum',4, 'intval'),
            'autoNext'    => $request->param('autoNext',0, 'intval'),
            'screenOn'    => $request->param('screenOn',0, 'intval'),
            'shockOn'     => $request->param('shockOn',1, 'intval'),
            'strict'      => $request->param('strict',1, 'intval')
        ];

        $validate = new ConfigureValidate();
        if(!$validate->scene('checkSetting')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ConfigureServer())->setting($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'设置失败');
        }
    }

    public function settingInfo(Request $request)
    {
        $param = [
            'imei'    => $request->param('imei','')
        ];

        $validate = new StatisticValidate();
        if(!$validate->scene('checkImei')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ConfigureServer())->settingInfo($param['imei']);

        ajax_info(0,'success', $response, false);
    }

    public function theme()
    {
        $response = (new ConfigureServer())->theme();

        ajax_info(0,'success', $response);
    }

    public function ring(Request $request)
    {
        $noise = $request->param('noise', 0, 'intval') ? 1 : 0;

        $response = (new ConfigureServer())->ring($noise);

        ajax_info(0,'success', $response);
    }
}