<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/5 23:32
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\ApplicationModel;
use app\api\model\ApplicationVersionModel;
use think\db\Expression;
use think\Exception;
use think\Log;

class ApplicationServer
{
    public function upload($param = [])
    {
        $where['uid'] = $param['uid'];
        $where['appkey'] = md5($param['uid'] . $param['package']);
        $sort = getRandStr(5) . $where['uid'];
        $package = $param['ext'] == 'apk' ? 'android' : 'ios';
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
                    ->field("a.appId, a.appName, a.appIcon, a.android, a.ios, a.size, max(v.version) version")
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
            $info['package'] = $value['android'] ? $value['android'] : $value['ios'];
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
        $appInfo = (new ApplicationModel())->field('appId, appkey, appName, appUrl, sortUrl, appIcon, size, describe, android, ios,appImage, state')->where($where)->find();


        if($appInfo){
            $appInfo = $appInfo->getData();
            $appInfo['appId'] = authcode($appInfo['appId'], 'ENCODE');
            $appInfo['appIcon'] = $appInfo['appIcon'] ? urlCompletion($appInfo['appIcon']) : '';
            $appInfo['sortUrl'] = $appInfo['sortUrl'] ? $appInfo['sortUrl'] : '';
            $appInfo['describe'] = $appInfo['describe'] ? $appInfo['describe'] : '';
            $appInfo['size'] = byteToMb($appInfo['size']);
            $appInfo['baseUrl'] = WEB_HTTP . '/g/';
            $appImage = [];
            $appInfo['appImage'] = $appInfo['appImage'] ? explode(',', $appInfo['appImage']) : [];
            foreach ($appInfo['appImage'] as &$val){
                $appImage[] = urlCompletion($val);
            }
            $appInfo['package'] = $appInfo['android'] ? $appInfo['android'] : $appInfo['ios'];
            unset($appInfo['android']);
            unset($appInfo['ios']);
            $appInfo['appImage'] = $appImage;
            $appInfo['version'] = "";
            $appInfo['versionList'] = [];
            foreach ($version as $k => $value){
                $value = $value->getData();
                if($k == 0)$appInfo['version'] = $value['version'];
                $value['apkId'] = authcode($value['apkId'], 'ENCODE');
                $value['remark'] = $value['remark'] ? $value['remark'] : '';
                $value['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
                $appInfo['versionList'][] = $value;
            }
        }else{
            $appInfo = [];
        }
        return $appInfo;
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
}