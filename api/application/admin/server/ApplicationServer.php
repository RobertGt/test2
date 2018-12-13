<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 17:31
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\ApplicationModel;
use app\admin\model\DownloadModel;
use app\admin\model\TempletModel;
use app\admin\model\VersionModel;
use app\api\model\UserMessageModel;
use think\Exception;
use think\Log;

class ApplicationServer
{
    public function appListByUser($param = [])
    {
        $where['uid'] = $param['uid'];
        if(!empty($param['seach']['appName'])){
            $where['appName'] = ['like', '%' . $param['seach']['appName'] . '%'];
        }

        $appModel = new ApplicationModel();
        $total = $appModel->where($where)->count();
        $list = $appModel
            ->where($where)
            ->field('appId id, appName, appUrl, size, download, android, ios, state, createTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['appUrl'] = urlCompletion($info['appUrl']);
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['packageName'] = $info['android'] ? $info['android']  : $info['ios'] ;
            $info['stateText'] = $info['state'] == 1 ? "<span class=\"label label-danger\">已禁用</span>" : "<span class=\"label label-primary\">应用中</span>";
            $info['size'] = $info['size'] ? round($info['size'] / 1024 / 1024, 2) . "MB" : "OMB";
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function appList($param = [])
    {
        $where = [];
        if(!empty($param['seach']['appName'])){
            $where['appName'] = ['like', '%' . $param['seach']['appName'] . '%'];
        }

        $appModel = new ApplicationModel();
        $total = $appModel->where($where)->count();
        $list = $appModel
            ->where($where)
            ->alias('a')
            ->join('bas_user u', 'u.uid = a.uid', 'LEFT')
            ->field('a.appId id, a.appName, a.appUrl, a.size, a.download, a.android, a.ios, a.state, a.createTime, u.uid, u.email user')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['appUrl'] = urlCompletion($info['appUrl']);
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['packageName'] = $info['android'] ? $info['android']  : $info['ios'] ;
            $info['stateText'] = $info['state'] == 1 ? "<span class=\"label label-danger\">已禁用</span>" : "<span class=\"label label-primary\">应用中</span>";
            $info['size'] = $info['size'] ? round($info['size'] / 1024 / 1024, 2) . "MB" : "OMB";
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function appStateUpdate($appId, $state = 0)
    {
        $appModel = new ApplicationModel();
        try{
            $where['appid'] = $appId;
            $appModel->save(['state' => $state], $where);
            if($state == 1){
                $templet = (new TempletModel())->where(['templetType' => 1, 'state' => 0])->find();
                if($templet){
                    $info = $appModel->where($where)->field('appName, uid')->find();
                    if($info){
                        $create['uid'] = $info['uid'];
                        $create['title'] = $templet['title'];
                        $create['type'] = 1;
                        $create['message'] = str_replace("{app}", $info['appName'], $templet['message']);
                        (new UserMessageModel())->create($create);
                    }
                }
            }
        }catch (Exception $e){
            Log::error("appStateUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function appInfo($id)
    {
        $where['appId'] = $id;

        $appInfo = (new ApplicationModel())->field('appId, appName, sortUrl, appIcon, describe, appImage, state')->where($where)->find();

        if($appInfo){
            $appInfo = $appInfo->getData();
            $appInfo['appIcon'] = $appInfo['appIcon'] ? urlCompletion($appInfo['appIcon']) : '../../static/img/app.png';
            $appInfo['sortUrl'] = urlCompletion('/' . $appInfo['sortUrl']);
            $appInfo['describe'] = $appInfo['describe'] ? $appInfo['describe'] : '';
            $appInfo['appId'] = md5($appInfo['appId']);
            $appInfo['appImage'] = explode(',', $appInfo['appImage']);
            foreach ($appInfo['appImage'] as &$val){
                $val = urlCompletion($val);
            }
        }else{
            $appInfo = [];
        }
        return $appInfo;
    }

    public function updateList($param = [])
    {
        $where['appId'] = $param['appId'];
        if(!empty($param['seach']['version'])){
            $where['version'] = ['like', '%' . $param['seach']['version'] . '%'];
        }

        $versionModel = new VersionModel();
        $total = $versionModel->where($where)->count();
        $list = $versionModel
            ->where($where)
            ->field('apkId id, version, size, appUrl, packageName, remark, platform, state, createTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['appUrl'] = urlCompletion($info['appUrl']);
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['packageName'] = $info['packageName'] ? $info['packageName']  : '' ;
            $info['stateText'] = $info['state'] == 1 ? "<span class=\"label label-danger\">上线</span>" : "<span class=\"label label-primary\">未上线</span>";
            $info['size'] = $info['size'] ? round($info['size'] / 1024 / 1024, 2) . "MB" : "OMB";
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function downList($param = [])
    {
        $where['d.appId'] = $param['appId'];

        $downloadModel = new DownloadModel();
        $total = $downloadModel->alias('d')->where($where)->count();
        $list = $downloadModel->alias('d')
            ->join('bas_application_version v', 'v.apkId = d.apkId', "LEFT")
            ->where($where)
            ->field('d.version, v.size, v.packageName, v.platform, d.createTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("d.createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['packageName'] = $info['packageName'] ? $info['packageName']  : '' ;
            $info['size'] = $info['size'] ? round($info['size'] / 1024 / 1024, 2) . "MB" : "OMB";
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }
}