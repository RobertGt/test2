<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 18:51
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\UserModel;
use app\api\model\UserDevicesModel;
use think\Exception;
use think\Log;

class UserServer
{
    public function userList($param = [])
    {
        $where = [];
        if(!empty($param['seach']['nickname'])){
            $where['nickname'] = ['like', '%' . $param['seach']['nickname'] . '%'];
        }
        if(!empty($param['seach']['sex'])){
            $where['sex'] = $param['seach']['sex'];
        }
        if(!empty($param['seach']['platform'])){
            $where['platform'] = $param['seach']['platform'];
        }
        $adminModel = new UserModel();
        $total = $adminModel->where($where)->count();
        $list = $adminModel->where($where)->field('uid id, nickname, avatar, sex, province, city, platform, createTime, updateTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        $sexArr = ['未知', '男', '女'];
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['updateTime'] = date('Y-m-d H:i:s', $info['updateTime']);
            $info['sex'] = isset($sexArr[$info['sex']]) ? $sexArr[$info['sex']] : '未知';
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function userDelete($uid)
    {
        $userModel = new UserModel();
        try{
            $userModel->startTrans();
            $where['uid'] = $uid;
            (new UserModel())->where($where)->delete();
            (new UserDevicesModel())->where($where)->delete();
            $userModel->commit();
        }catch (Exception $e){
            $userModel->rollback();
            Log::error("userDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}