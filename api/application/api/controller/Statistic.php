<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/12 23:47
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\StatisticServer;
use app\api\validate\StatisticValidate;
use think\Request;

class Statistic extends Base
{
    public function taskStatistic(Request $request)
    {
        $param = [
            'imei'    => $request->param('imei','')
        ];

        $validate = new StatisticValidate();
        if(!$validate->scene('checkImei')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new StatisticServer())->taskStatistic($param['imei']);

        ajax_info(0,'success', $response, false);
    }

    public function taskDistribution(Request $request)
    {
        $param = [
            'imei'    => $request->param('imei',''),
            'dateType'=> $request->param('dateType',0, 'intval'),
            'pastTime'=> $request->param('pastTime',0, 'intval')
        ];

        $validate = new StatisticValidate();
        if(!$validate->scene('taskDistribution')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new StatisticServer())->taskDistribution($param);

        ajax_info(0,'success', $response);
    }

    public function taskCurve(Request $request)
    {
        $param = [
            'imei'    => $request->param('imei',''),
            'dateType'=> $request->param('dateType',0, 'intval')
        ];
        $validate = new StatisticValidate();
        if(!$validate->scene('checkDateType')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new StatisticServer())->taskCurve($param);

        ajax_info(0,'success', $response);
    }

    public function coveStatistic(Request $request)
    {
        $param = [
            'imei'    => $request->param('imei','')
        ];

        $validate = new StatisticValidate();
        if(!$validate->scene('checkImei')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new StatisticServer())->taskStatistic($param['imei'], 1);

        ajax_info(0,'success', $response, false);
    }

    public function coveCurve(Request $request)
    {
        $param = [
            'imei'    => $request->param('imei',''),
            'dateType'=> $request->param('dateType',0, 'intval')
        ];
        $validate = new StatisticValidate();
        if(!$validate->scene('checkDateType')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new StatisticServer())->taskCurve($param, 1);

        ajax_info(0,'success', $response);
    }
}