<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 1:53
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\TempletModel;
use app\admin\model\UserModel;
use app\api\model\UserMessageModel;
use think\Exception;
use think\Log;

class TempletServer
{
    public function templetList($param = [])
    {

        $templetModel = new TempletModel();
        $total = $templetModel->count();
        $list = $templetModel
            ->page($param['pageNum'], $param['pageSize'])
            ->field("templetId id, templetType, title, message, state, updateTime")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['updateTime'] = date('Y-m-d H:i:s', $info['updateTime']);
            $info['stateText'] = $info['state'] == 1 ? "<span class=\"label label-danger\">已禁用</span>" : "<span class=\"label label-primary\">应用中</span>";
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function templetUpdateState($appId, $state = 0)
    {
        $templetModel = new TempletModel();
        try{
            $where['templetId'] = $appId;
            $templetModel->save(['state' => $state], $where);
        }catch (Exception $e){
            Log::error("templetUpdateState error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function templetInfo($id)
    {
        $where['templetId'] = $id;

        $templetInfo = (new TempletModel())->field('templetId id, templetType, title, message')->where($where)->find();

        if($templetInfo){
            $templetInfo = $templetInfo->getData();
            $templetInfo['message'] = $templetInfo['message'] ? $templetInfo['message'] : '';
        }else{
            $templetInfo = [];
        }
        return $templetInfo;
    }

    public function templetUpdate($param = [])
    {
        $save['message'] = $param['message'];
        try{
            (new TempletModel())->save($save, ['templetId' => $param['id']]);
            if($param['templetType'] == 3 && $param['send'] == 1){
                $users = (new UserModel())->where(['state' => 0])->field('uid')->select();
                $message = [];
                foreach ($users as $k => $v){
                    $c['uid'] = $v['uid'];
                    $c['title'] = $param['title'];
                    $c['type'] = 3;
                    $c['message'] = $save['message'];
                    $message[] = $c;
                }
                if($message)(new UserMessageModel())->saveAll($message);
            }
        }catch (Exception $e){
            Log::error("templetUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}