<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 2:41
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\ApplicationModel;
use app\admin\model\DownloadModel;
use app\admin\model\RenewalModel;
use app\admin\model\UserModel;
use think\cache\driver\Redis;

class StatisticsServer
{
    public function index()
    {
        $time = strtotime(date('Y-m-d'));
        $user = (new UserModel())->field("count(*) total, sum(createTime >= {$time}) day")->find()->toArray();
        $response['dayUser'] = (int)$user['day'];
        $response['totalUser'] = (int)$user['total'];
        $redis = new Redis();
        $redis->handler()->select(1);
        $keyWithUserPrefix = $redis->handler()->keys('token_*');
        $user = [];
        foreach ($keyWithUserPrefix as $val){
            $uid = $redis->handler()->get($val);
            if(!$uid)continue;
            $user[$uid] = 1;
        }
        $online = count($user);
        $response['online'] = $online;
        $response['upload'] = (new ApplicationModel())->count();
        $response['download'] =  (new DownloadModel())->count();
        $price = (new RenewalModel())->where(['state' => 1])->sum('price');
        $response['price'] = sprintf("%.2f", $price / 100);
        return $response;
    }

    public function chart()
    {
        $time = strtotime(date('Y-m-d H:00:00', time()));
        $timeArr = [];
        for ($i = 0; $i <= 23; $i++){
            $timeArr[] = date('YmdH', $time);
            $time = strtotime('-1 hour', $time);
        }
        sort($timeArr);
        $time = strtotime('+1 hour', $time);
        //用户
        $user = (new UserModel())->where("createTime >= {$time}")->field("count(*) total, FROM_UNIXTIME(createTime, '%Y%m%d%h') time")->group("FROM_UNIXTIME(createTime, '%Y%m%d%h')")->select();
        $response['user'] = [];
        foreach ($timeArr as $k => $v){
            $response['user'][$k] = 0;
            foreach ($user as $key => $val){
                if($v == $val['time']){
                    $response['user'][$k] = (int)$val['total'];
                    unset($user[$key]);
                }
            }
        }

        $upload = (new ApplicationModel())->where("createTime >= {$time}")->field("count(*) total, FROM_UNIXTIME(createTime, '%Y%m%d%h') time")->group("FROM_UNIXTIME(createTime, '%Y%m%d%h')")->select();
        $response['upload'] = [];
        foreach ($timeArr as $k => $v){
            $response['upload'][$k] = 0;
            foreach ($upload as $key => $val){
                if($v == $val['time']){
                    $response['upload'][$k] = (int)$val['total'];
                    unset($upload[$key]);
                }
            }
        }

        $download = (new DownloadModel())->where("createTime >= {$time}")->field("count(*) total, FROM_UNIXTIME(createTime, '%Y%m%d%h') time")->group("FROM_UNIXTIME(createTime, '%Y%m%d%h')")->select();
        $response['download'] = [];
        foreach ($timeArr as $k => $v){
            $response['download'][$k] = 0;
            foreach ($download as $key => $val){
                if($v == $val['time']){
                    $response['download'][$k] = (int)$val['total'];
                    unset($download[$key]);
                }
            }
        }

        $price = (new RenewalModel())->where("createTime >= {$time} and state = 1")->field("sum(price) total, FROM_UNIXTIME(createTime, '%Y%m%d%h') time")->group("FROM_UNIXTIME(createTime, '%Y%m%d%h')")->select();
        $response['price'] = [];
        foreach ($timeArr as $k => $v){
            $response['price'][$k] = "0.00";
            foreach ($price as $key => $val){
                if($v == $val['time']){
                    $response['price'][$k] = sprintf("%.2f", $val['total'] / 100);
                    unset($price[$key]);
                }
            }
        }

        foreach ($timeArr as &$value){
            $value = substr($value, -2);
        }

        $response['time'] = $timeArr;

        return $response;
    }

    public function buyTop()
    {
        $sql = "select t.total,p.packageName from (SELECT count(*) total,packageId FROM bas_user_renewals where state = 1 GROUP BY packageId ORDER BY total desc limit 10) t join bas_package p on t.packageId = p.packageId ORDER BY t.total desc";
        $top10 = (new RenewalModel())->query($sql);
        $response = [];
        foreach ($top10 as $value){
            $response[] = $value;
        }
        return $response;
    }

    public function downTop()
    {
        $sql = "select t.total,a.appName from (SELECT count(*) total,appId FROM bas_application_down GROUP BY appId ORDER BY total desc limit 10) t join bas_application a on t.appId = a.appId ORDER BY t.total desc";
        $top10 = (new DownloadModel())->query($sql);
        $response = [];
        foreach ($top10 as $value){
            $response[] = $value;
        }
        return $response;
    }

    public function eventTop()
    {
        $sql = "select u.uid,u.email,a.appName,a.createTime from bas_application a JOIN bas_user u on a.uid = u.uid ORDER BY createTime desc limit 10";
        $top10 = (new ApplicationModel())->query($sql);
        $response = [];
        foreach ($top10 as $value){
            $value['format'] = format_date($value['createTime']);
            $value['createTime'] = date("Y-m-d H:i:s", $value['createTime']);
            $response[] = $value;
        }
        return $response;
    }
}