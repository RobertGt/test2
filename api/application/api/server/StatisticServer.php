<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/14 22:07
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\TaskFinishModel;

class StatisticServer
{
    public function taskStatistic($imei = '', $coves = 0)
    {
        $statistic = (new TaskFinishModel())->taskStatistic($imei, $coves);

        if($coves){
            $resp = $statistic['absorbed'];
            $response['absCount'] = !empty($resp['absCount']) ? round($resp['absCount'] / 3600, 2) : 0;
            $response['absWeek'] = !empty($resp['absWeek']) ? round($resp['absWeek'] / 3600, 2) : 0;
            $response['absDay'] = !empty($resp['absDay']) ? round($resp['absDay'] / 3600, 2): 0;
        }

        $resp = $statistic['statistic'];
        $response['finish'] = !empty($resp['finish']) ? (int)$resp['finish'] : 0;
        $response['week'] = !empty($resp['week']) ? (int)$resp['week'] : 0;
        $response['today'] = !empty($resp['today']) ? (int)$resp['today'] : 0;


        return $response;
    }

    public function taskDistribution($param = [])
    {
        if($param['dateType'] == 1){
            $start = strtotime(date('Y-m-d', strtotime("this week Monday")));
            $unit = 'week';
        }else if($param['dateType'] == 2){
            $start = strtotime(date('Y-m-01'));
            $unit = 'month';
        }else{
            $start = strtotime(date('Y-m-d'));
            $unit = 'day';
        }
        $end = strtotime(date('Y-m-d', strtotime("+1 day")));

        if($param['pastTime']){
            $end = $start;
            $start = strtotime(date('Y-m-d', strtotime("-1 {$unit}", $end)));
        }

        $distribution = (new TaskFinishModel())->taskDistribution($param['imei'], $start, $end);

        $response = [];
        foreach ($distribution as $value){
            $info = $value->getData();
            $info['hour'] = round($info['hour'] / 3600, 2);
            $response[] = $info;
        }

        return $response;
    }

    public function taskCurve($param = [], $coves = 0)
    {
        $taskFinishModel = new TaskFinishModel();
        $date = [];
        $end = strtotime(date('Y-m-d', strtotime("+1 day")));

        if($param['dateType'] == 2){
            $start = $taskFinishModel->minUploadTime($param['imei']);
            $startTime = date('Ym01', $start);
            $endTime = date('Ym01', $end);
            for ($i = $startTime; $i <= $endTime; $i = date('Ym01', strtotime("+1 month", strtotime($i)))){	
                $date[] = date('Y年m月', strtotime($i));
            }
            $unit = 'Y年m月';
        }else if ($param['dateType'] == 1){
            $start = strtotime(date("Y-m-d", strtotime("-3 week", strtotime("this week Monday"))));
            $startTime = date('Ymd', $start);
            $endTime = date('Ymd', time());
            for ($i = $startTime; $i < $endTime; $i = date('Ymd', strtotime("+1 week", strtotime($i)))){
                $date[] = [date('Y-m-d', strtotime($i)), date('Y-m-d', strtotime("+6 day", strtotime($i)))];
            }
            $unit = '';
        }else{
            $start = strtotime(date("Y-m-d", strtotime("-6 day")));
            for ($i = 6; $i >= 0; $i--){
                $date[] = date('m月d日', strtotime("-{$i} day"));
            }
            $unit = 'm月d日';
        }

        $taskList = $taskFinishModel->taskCurve($param['imei'], $start, $end, $coves);

        $response['date'] = [];
        $response['num'] = [];
        $response['max'] = 0;
        $response['avg'] = 0;
        $total = 0;
        foreach ($date as $item){
            $response['date'][] = is_array($item) ? date('m月d日', strtotime($item[0])) . '-' . date('m月d日', strtotime($item[1])) : $item;
            $num = 0;
            foreach ($taskList as $value){
                $info = $value->getData();
                if($unit){
                    if(date($unit, $info['uploadTime']) == $item){
                        $num += (int)$info['total'];
                    }
                }else{
                    $time = strtotime(date('Y-m-d', $info['uploadTime']));
                    $start = strtotime($item[0]);
                    $end = strtotime($item[1]);
                    if($time >=  $start && $time <= $end){
                        $num += (int)$info['total'];
                    }
                }
            }
            $response['max'] = max($response['max'], $num);
            $response['num'][] = $num;
            $total += $num;
        }
	
        $response['avg'] = count($response['num']) ? round($total / count($response['num']), 1) : 0;

        return $response;
    }
}