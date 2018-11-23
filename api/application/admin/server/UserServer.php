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
        if(!empty($param['seach']['email'])){
            $where['email'] = ['like', '%' . $param['seach']['email'] . '%'];
        }
        if(!empty($param['seach']['mobile'])){
            $where['mobile'] = ['like', '%' . $param['seach']['mobile'] . '%'];
        }
        if(!empty($param['seach']['state'])){
            $where['state'] = ($param['seach']['state'] - 1);
        }
        if(!empty($param['seach']['realname'])){
            $where['realname'] = ($param['seach']['realname'] - 1);
        }
        $adminModel = new UserModel();
        $total = $adminModel->where($where)->count();
        $list = $adminModel->alias('u')
            ->join('bas_package p', 'u.packageId = p.packageId', 'LEFT')
            ->where($where)
            ->field('u.uid id, u.email, u.mobile, u.state, u.realname, u.packageId, p.packageName, u.surplus, u.createTime, u.loginTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['loginTime'] = $info['loginTime'] ? date('Y-m-d H:i:s', $info['loginTime']) : '';
            $info['packageName'] = $info['packageName'] ? $info['packageName'] : '';
            $info['stateText'] = $info['state'] == 1 ? "<span class=\"label label-danger\">禁用</span>" : "<span class=\"label label-primary\">正常</span>";
            $info['realnameText'] = $info['realname'] == 1 ? "<span class=\"label label-primary\">通过</span>" :
                                            ($info['realname'] == 2 ? "<span class=\"label label-danger\">失败</span>" : "<span class=\"label label-success\">待审核</span>");
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