<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/9 4:00
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\UserMessageModel;
use think\Exception;
use think\Log;

class MessageServer
{
    public function messageList($param = [], $uid = 0)
    {
        $where['uid'] = $uid;
        $total = (new UserMessageModel())->where($where)->count();
        $messageList = (new UserMessageModel())->where($where)
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($messageList as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['state'] = $info['state'] ? '已读' : '未读';
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function messageUpdate($recId, $uid)
    {
        $recId = explode(',', $recId);
        if(!$recId)return false;
        $where['uid'] = $uid;
        $where['recId'] = ['in', $recId];
        try{
            (new UserMessageModel())->save(['state' => 1], $where);
        }catch (Exception $e){
            Log::error("messageUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function messageFind($uid)
    {
        $where['uid'] = $uid;
        $where['state'] = 0;
        $where['createTime'] = ['egt', strtotime('-15 day')];
        $messageInfo = (new UserMessageModel())->where($where)
            ->order("createTime desc")
            ->find();
        if(!$messageInfo){
            return false;
        }
        return $messageInfo->toArray();
    }
}