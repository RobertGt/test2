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
use think\Exception;
use think\Log;

class ApplicationServer
{
    public function upload($param = [])
    {
        $where['uid'] = $param['uid'];
        $where['appkey'] = md5($param['uid'] . $param['package']);
        $sort = "qwaasd";
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
                $create['appUrl']  = WEB_HTTP . '/g/' . $sort ;
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
}