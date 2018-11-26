<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 2:39
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\StatisticsServer;
use think\Request;

class Statistics extends Base
{
    public function index()
    {
        $response = (new StatisticsServer())->index();

        ajax_info(0,'success', $response);
    }

    public function chart()
    {
        $response = (new StatisticsServer())->chart();

        ajax_info(0,'success', $response);
    }

    public function buyTop()
    {
        $response = (new StatisticsServer())->buyTop();

        ajax_info(0,'success', $response);
    }

    public function downTop()
    {
        $response = (new StatisticsServer())->downTop();

        ajax_info(0,'success', $response);
    }

    public function eventTop()
    {
        $response = (new StatisticsServer())->eventTop();

        ajax_info(0,'success', $response);
    }
}