<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/5 23:32
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\extend\HttpClient;
use app\api\model\ApplicationDownModel;
use app\api\model\ApplicationModel;
use app\api\model\ApplicationVersionModel;
use app\api\model\UserModel;
use think\Config;
use think\db\Expression;
use think\Exception;
use think\Log;

class ApplicationServer
{
    public function upload($param = [])
    {
        $where['uid'] = $param['uid'];
        $package = $param['ext'] == 'apk' ? 'android' : 'ios';
        $where['appkey'] = md5($param['uid'] . $param['package'] . $package);
        $sort = getRandStr(5) . $where['uid'];
        $where[$package] = $param['package'];
        $applicationModel = new ApplicationModel();
        $appInfo = $applicationModel->where($where)->field('appId')->find();
        try{
            $applicationModel->startTrans();
            if(empty($appInfo)){
                $create['appkey']  = $where['appkey'];
                $create['uid']     = $param['uid'];
                $create['appName'] = $param['appName'];
                $create['appIcon'] = $param['icon'];
                $create['appUrl']  = WEB_HTTP . '/g/' . $where['appkey'];
                $create['sortUrl'] = $sort;
                $create[$package] = $param['package'];
                $create['size'] = $param['size'];
                $create['defaultPlatform'] = $package;
                $app = $applicationModel->create($create);
                $appInfo['appId'] = $app->appId;
            }
            $version['appId'] = $appInfo['appId'];
            $version['code']  =  $param['code'];
            $version['version'] = $param['version'];
            $version['size'] = $param['size'];
            $version['appUrl'] = $param['path'];
            $version['packageName'] = $param['package'];
            $version['platform'] = $package;
            (new ApplicationVersionModel())->create($version);
            $applicationModel->commit();
        }catch (Exception $e){
            $applicationModel->rollback();
            Log::error("upload error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appList($param = [], $uid = 0)
    {
        $where['uid'] = $uid;
        if(!empty($param['appName'])){
            $where['a.appName'] = ['like', '%' . $param['appName'] . '%'];
        }
        if($param['platform']){
            $platform = $param['platform'] == 'ios' ? 'a.ios' : 'a.android';
            $where[$platform] = ['exp', new Expression('is not null')];
        }
        $appList = (new ApplicationModel())->alias('a')->where($where)
                    ->join('bas_application_version v', 'v.appId = a.appId')
                    ->field("a.appId, a.appName, a.appIcon, a.android, a.ios, a.size, max(v.version) version, a.defaultPlatform")
                    ->page($param['pageNum'], $param['pageSize'])
                    ->group('a.appId')
                    ->order("a.createTime desc")
                    ->select();
        $response = [];
        foreach ($appList as $row){
            $value = $row->getData();
            $info['appId'] = authcode($value['appId'], 'ENCODE');
            $info['appName'] = $value['appName'] ? $value['appName'] : "";
            $info['appIcon'] = urlCompletion($value['appIcon']);
            $info['size'] = byteToMb($value['size']);
            $info['version'] = $value['version'];
            $info['platform'] = [];
            if($value['android'])$info['platform'][] = 'android';
            if($value['ios'])$info['platform'][] = 'ios';
            $info['package'] = $value[$value['defaultPlatform']];
            $response[] = $info;
        }
        return $response;
    }

    public function userAppList($appId, $uid)
    {
        $where['appId'] = ['neq', $appId];
        $where['uid'] = $uid;
        $appList = (new ApplicationModel())->where($where)
            ->field("appName, appIcon, sortUrl, android, ios")
            ->order("createTime desc")
            ->select();
        $response = [];
        foreach ($appList as $row){
            $value = $row->getData();
            $info['appName'] = $value['appName'] ? $value['appName'] : "";
            $info['appIcon'] = urlCompletion($value['appIcon']);
            $info['sortUrl'] = $value['sortUrl'];
            $info['platform'] = [];
            if($value['android'])$info['platform'][] = 'android';
            if($value['ios'])$info['platform'][] = 'ios';
            $response[] = $info;
        }
        return $response;
    }

    public function appDelete($appId, $uid)
    {
        try{
            $where['appId'] = $appId;
            $where['uid'] = $uid;
            (new ApplicationModel())->where($where)->delete();
            //(new ApplicationVersionModel())->where(['appId' => $appId])->delete();
        }catch (Exception $e){
            Log::error("appDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appInfo($appId = 0 , $uid = 0)
    {
        $where['appId'] = $appId;
        $version = (new ApplicationVersionModel())->where($where)->field('apkId, version, createTime, remark, state')->order('version desc')->select();
        $where['uid'] = $uid;
        $appInfo = (new ApplicationModel())->field('appId, appkey, appName, appUrl, sortUrl, appIcon, size, describe, android, ios,appImage, state, defaultPlatform')->where($where)->find();


        if($appInfo){
            $appInfo = $appInfo->getData();
            $appInfo['appId'] = authcode($appInfo['appId'], 'ENCODE');
            $appInfo['sortUrl'] = $appInfo['sortUrl'] ? $appInfo['sortUrl'] : '';
            $appInfo['describe'] = $appInfo['describe'] ? $appInfo['describe'] : '';
            $appInfo['size'] = byteToMb($appInfo['size']);
            $appInfo['baseUrl'] = WEB_HTTP . '/';
            $appInfo['qrCode'] = urlCompletion(qrCode($appInfo['baseUrl'] . $appInfo['sortUrl'], $appInfo['appIcon']));
            $appInfo['appIcon'] = $appInfo['appIcon'] ? urlCompletion($appInfo['appIcon']) : '';
            $appImage = [];
            $appInfo['appImage'] = $appInfo['appImage'] ? explode(',', $appInfo['appImage']) : [];
            foreach ($appInfo['appImage'] as &$val){
                $appImage[] = urlCompletion($val);
            }
            $appInfo['package'] = $appInfo[$appInfo['defaultPlatform']];
            unset($appInfo['android']);
            unset($appInfo['ios']);
            $appInfo['appImage'] = $appImage;
            $appInfo['version'] = "";
            $appInfo['versionList'] = [];
            foreach ($version as $k => $value){
                $value = $value->getData();
                if($k == 0)$appInfo['version'] = $value['version'];
                $value['apkId'] = authcode($value['apkId'], 'ENCODE');
                $value['appId'] = $appInfo['appId'];
                $value['remark'] = $value['remark'] ? $value['remark'] : '';
                $value['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
                $appInfo['versionList'][] = $value;
            }
        }else{
            $appInfo = [];
        }
        return $appInfo;
    }

    public function appDownInfo($appId = 0)
    {
        if(authcode($appId)){
            $where['appId'] = authcode($appId);
        }else{
            $where['sortUrl'] = $appId;
        }
        $appInfo = (new ApplicationModel())
            ->field('appId, appkey, appName, appUrl, sortUrl, appIcon, size, describe, android, ios, state, defaultPlatform')
            ->where($where)->find();

        if($appInfo){
            $appInfo = $appInfo->getData();
            $version = (new ApplicationVersionModel())->where(['appId' => $appInfo['appId']])->field('apkId, version, createTime, remark, state')->order('version desc')->find()->getData();
            $appInfo['appId'] = authcode($appInfo['appId'], 'ENCODE');
            $appInfo['sortUrl'] = $appInfo['sortUrl'] ? $appInfo['sortUrl'] : '';
            $appInfo['describe'] = $appInfo['describe'] ? $appInfo['describe'] : '';
            $appInfo['size'] = byteToMb($appInfo['size']);
            $appInfo['baseUrl'] = WEB_HTTP . '/';
            $appInfo['qrCode'] = urlCompletion(qrCode($appInfo['baseUrl'] . $appInfo['sortUrl'], $appInfo['appIcon']));
            $appInfo['appIcon'] = $appInfo['appIcon'] ? urlCompletion($appInfo['appIcon']) : '';
            $appInfo['platform'] = [];
            if($appInfo['android'])$appInfo['platform'][] = 'android';
            if($appInfo['ios'])$appInfo['platform'][] = 'ios';
            $appInfo['version'] = $version['version'];
            $appInfo['createTime'] = date('Y-m-d H:i:s', $version['createTime']);
        }else{
            $appInfo = [];
        }
        return $appInfo;
    }

    public function appDownUrl($param = [])
    {
        if(authcode($param['appId'])){
            $where['appId'] = authcode($param['appId']);
        }else{
            $where['sortUrl'] = $param['appId'];
        }
        $where['state'] = 0;
        $appInfo = (new ApplicationModel())
            ->field('appId, uid')
            ->where($where)->find();
        if(!$appInfo){
            $this->errMsg = '下载应用已被禁用或者不存在';
            return false;
        }
        unset($where['sortUrl']);
        $where['appId'] = $appInfo['appId'];
        $userInfo = (new UserModel())
            ->where(['uid' => $appInfo['uid']])->field('download, surplus, expireTime')->find();
        if(!$userInfo){
            $this->errMsg = '下载应用已被禁用或者不存在';
            return false;
        }
        if($userInfo['expireTime'] < time()){
            $userInfo['download'] = Config::get('default.download');
        }
        $w['appId'] = $appInfo['appId'];
        $w['createTime'] = ['egt', strtotime(date('Y-m-d'))];
        $surplus = (new ApplicationDownModel())
            ->where($w)
            ->count();
        if($surplus >= $userInfo['download'] + $userInfo['surplus']){
            $this->errMsg = '下载点数不足';
            return false;
        }
        $where['platform'] = $param['platform'];
        $where['state'] = 1;

        $version = (new ApplicationVersionModel())->where($where)->field('apkId, version, appUrl')->order('version desc')->find();
        if(!$version){
            $this->errMsg = '下载平台不存在';
            return false;
        }
        $create['appId'] = $appInfo['appId'];
        $create['apkId'] = $version['apkId'];
        $create['version'] = $version['version'];
        $create['lng'] = $param['lng'];
        $create['lat'] = $param['lat'];
        $create['prov'] = '未知';
        $create['city'] = '未知';
        $create['region'] = '未知';
        if($create['lat'] && $create['lng']){
            $url = sprintf(Config::get('baidu_map_url'), $create['lat'] . ',' . $create['lng']);
            $http = new HttpClient();
            $rep = $http->Request($url);
            $rep = json_decode($rep, true);
            if($rep && $rep['status'] == 0 && isset($rep['result']['location']['lat']) && isset($rep['result']['location']['lng']) && isset($rep['result']['addressComponent'])){
                $create['prov'] = $rep['result']['addressComponent']['province'] ? $rep['result']['addressComponent']['province'] : '';
                $create['city'] = $rep['result']['addressComponent']['city'] ? $rep['result']['addressComponent']['city'] : '';
                $create['region'] = $rep['result']['addressComponent']['district'] ? $rep['result']['addressComponent']['district'] : '';
            }
        }
        try{
            (new ApplicationDownModel())->create($create);
            if($surplus >= $userInfo['download']){
                (new UserModel())->where(['uid' => $appInfo['uid']])->exp('surplus', 'surplus - 1')->update();
            }
            $response['url'] = urlCompletion($version['appUrl']);
        }catch (Exception $e){
            Log::error("appDownUrl error:" . $e->getMessage());
            $this->errMsg = '未知错误';
            return false;
        }
        return $response;
    }

    public function appVersionRemark($param = [])
    {
        $where['apkId'] = $param['appId'];
        try{
            $save['remark'] = $param['remark'];
            (new ApplicationVersionModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("appVersionRemark error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appStateUpdate($param = [])
    {
        $where['apkId'] = $param['appId'];
        try{
            $save['state'] = $param['state'];
            (new ApplicationVersionModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("appStateUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appUrlUpdate($param = [], $uid)
    {
        $where['appId'] = $param['appId'];
        $where['uid'] = $uid;
        try{
            $save['appUrl'] = $param['appUrl'];
            (new ApplicationModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("appUrlUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appUpdate($param = [], $uid)
    {
        $where['appId'] = $param['appId'];
        $where['uid'] = $uid;
        $save['appName'] = $param['appName'];
        $save['sortUrl'] = $param['sortUrl'];
        if($param['appIcon'])$save['appIcon'] = $param['appIcon'];
        $save['describe'] = $param['describe'];
        $save['appImage'] = trim($param['appImage'], ",");
        try{
            (new ApplicationModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("appUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appMerge($param = [], $merge = [])
    {
        $where['uid'] = $param['uid'];
        $where['appId'] = $param['appId'];
        $appModel = new ApplicationModel();
        try{
            $appModel->startTrans();
            $save[$merge['defaultPlatform']] = $merge[$merge['defaultPlatform']];
            $default = $appModel->save($save, $where);
            $where['appId'] = $merge['appId'];
            $mergeDel = (new ApplicationModel())->where($where)->delete();
            $mergeUpdate = (new ApplicationVersionModel())->save(['appId' => $param['appId']], ['appId' => $merge['appId']]);
            (new ApplicationDownModel())->save(['appId' => $param['appId']], ['appId' => $merge['appId']]);
            if($default && $mergeDel && $mergeUpdate){
                $appModel->commit();
            }else{
                $appModel->startTrans();
                return false;
            }
        }catch (Exception $e){
            Log::error("appMerge error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appStatistics($param)
    {
        if($param['type'] >= 2){
            $u = 'Y-m-d';
            $start = strtotime(date($u, strtotime('-' . ($param['type'] - 1) . ' day')));
            $unit = ' day';
            $f = '%Y-%m-%d';
        }else{
            $param['type'] = 24;
            $start = strtotime(date('Y-m-d H:00:00', strtotime('-23 hours')));
            $unit = ' hours';
            $u = 'H';
            $f = '%h';
        }
        $timeArr = [];
        for ($i = 0; $i < $param['type']; $i++){
            $timeArr[] = date($u, strtotime('+' . $i . $unit, $start));
        }
        $where['appId'] = $param['appId'];
        $where['createTime'] = ['egt', $start];
        $statistics = (new ApplicationDownModel())->where($where)
            ->field("count(*) total, FROM_UNIXTIME(createTime,\"{$f}\") time")
            ->group("FROM_UNIXTIME(createTime,\"{$f}\")")
            ->select();
        $data = [];
        foreach ($timeArr as $k => $v){
            $data[$k] = 0;
            foreach ($statistics as $key => $val){
                if($val['time'] == $v){
                    $data[$k] = (int)$val['total'];
                    unset($statistics[$key]);
                }
            }
        }
        $response['time'] = $timeArr;
        $response['data'] = $data;

        $prov = [
            [
                'name' => '安徽',
                'lng'  => '117.29172',
                'lat'  => '31.86646',
            ], [
                'name' => '北京',
                'lng'  => '116.40969',
                'lat'  => '39.89945',
            ], [
                'name' => '福建',
                'lng'  => '119.30491',
                'lat'  => '26.07912',
            ], [
                'name' => '甘肃',
                'lng'  => '103.77926',
                'lat'  => '36.07967',
            ], [
                'name' => '广东',
                'lng'  => '113.26006',
                'lat'  => '23.13399',
            ], [
                'name' => '广西',
                'lng'  => '108.33199',
                'lat'  => '22.82527',
            ], [
                'name' => '贵州',
                'lng'  => '106.7139',
                'lat'  => '26.58719',
            ], [
                'name' => '海南',
                'lng'  => '110.35734',
                'lat'  => '20.04501',
            ], [
                'name' => '河北',
                'lng'  => '114.52002',
                'lat'  => '39.14307',
            ], [
                'name' => '河南',
                'lng'  => '113.67769',
                'lat'  => '34.76714',
            ], [
                'name' => '黑龙江',
                'lng'  => '126.66254',
                'lat'  => '45.73957',
            ], [
                'name' => '湖北',
                'lng'  => '114.28742',
                'lat'  => '30.58965',
            ], [
                'name' => '湖南',
                'lng'  => '112.99369',
                'lat'  => '28.19602',
            ], [
                'name' => '吉林',
                'lng'  => '125.32512',
                'lat'  => '43.8929',
            ], [
                'name' => '江苏',
                'lng'  => '118.79349',
                'lat'  => '32.05892',
            ], [
                'name' => '江西',
                'lng'  => '115.91655',
                'lat'  => '28.66246',
            ], [
                'name' => '辽宁',
                'lng'  => '123.38035',
                'lat'  => '41.81073',
            ], [
                'name' => '内蒙古',
                'lng'  => '111.68104',
                'lat'  => '40.81635',
            ], [
                'name' => '宁夏',
                'lng'  => '106.28956',
                'lat'  => '38.47426',
            ], [
                'name' => '青海',
                'lng'  => '101.80335',
                'lat'  => '36.62695',
            ], [
                'name' => '山东',
                'lng'  => '116.97009',
                'lat'  => '36.65319',
            ], [
                'name' => '山西',
                'lng'  => '112.59966',
                'lat'  => '37.87319',
            ], [
                'name' => '陕西',
                'lng'  => '108.9976',
                'lat'  => '34.30017',
            ], [
                'name' => '上海',
                'lng'  => '121.4755',
                'lat'  => '31.23385',
            ], [
                'name' => '四川',
                'lng'  => '104.07043',
                'lat'  => '30.66431',
            ], [
                'name' => '台湾',
                'lng'  => '121.03795',
                'lat'  => '23.60374',
            ], [
                'name' => '天津',
                'lng'  => '117.2001',
                'lat'  => '39.14307',
            ], [
                'name' => '西藏',
                'lng'  => '91.12836',
                'lat'  => '29.67571',
            ], [
                'name' => '新疆',
                'lng'  => '87.61988',
                'lat'  => '43.80553',
            ], [
                'name' => '云南',
                'lng'  => '102.71037',
                'lat'  => '25.03736',
            ], [
                'name' => '浙江',
                'lng'  => '120.1673',
                'lat'  => '30.25748',
            ], [
                'name' => '重庆',
                'lng'  => '106.55355',
                'lat'  => '29.56029',
            ], [
                'name' => '香港',
                'lng'  => '114.18144',
                'lat'  => '22.31881',
            ], [
                'name' => '澳门',
                'lng'  => '113.55438',
                'lat'  => '22.19907',
            ],
        ];
        $region = (new ApplicationDownModel())->where($where)
            ->field("count(*) total, prov")
            ->group("prov")
            ->select();
        $city = [];
        foreach ($prov as $k => $v){
            $city[$k]['name'] = $v['name'];
            $city[$k]['value'][0] = $v['lng'];
            $city[$k]['value'][1] = $v['lat'];
            $city[$k]['value'][2] = 0;
            foreach ($region as $key => $val){
                if(strpos($val['prov'], $v['name']) !== false){
                    $city[$k]['value'][2] = (int)$val['total'];
                    unset($region[$key]);
                }
            }
        }
        $response['prov'] = $city;
        $city = (new ApplicationDownModel())->where($where)
            ->field("count(*) total, city")
            ->group("city")
            ->order("total")
            ->limit(10)
            ->select();
        $response['city'] = [];
        foreach ($city as $k => $value){
            $response['city'][] = [
                'name'  => $value['city'] ? $value['city'] : '未知',
                'total' => $value['total'],
            ];
        }
        return $response;
    }
}