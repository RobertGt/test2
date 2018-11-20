<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/18 23:38
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\AdminModel;
use think\Exception;
use think\Log;

class AdminServer
{
    public function adminInfo($id = 0, $token = '')
    {
        $where = [];
        if($id){
            $where['aid'] = $id;
        }else{
            $where['token'] = $token;
        }

        $adminInfo = (new AdminModel())->field('aid, account, password, createTime, remark')->where($where)->find();

        if($adminInfo){
            $adminInfo = $adminInfo->getData();
            $adminInfo['createTime'] = date('Y-m-d H:i:s', $adminInfo['createTime']);
        }else{
            $adminInfo = [];
        }
        return $adminInfo;
    }

    public function adminList($param = [])
    {
        $where = [];
        if(!empty($param['seach']['account'])){
            $where['account'] = ['like', '%' . $param['seach']['account'] . '%'];
        }
        $adminModel = new AdminModel();
        $total = $adminModel->where($where)->count();
        $list = $adminModel->where($where)->field('aid id, account, remark, createTime, loginTime, loginIp, loginCount, remark')
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
            $info['remark'] = $info['remark'] ? $info['remark']  : '';
            $info['loginIp'] = $info['loginIp'] ? $info['loginIp']  : '';
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function login($param = [])
    {
        $checkPassword = $this->checkPassword($param['account'], $param['password']);
        if(!$checkPassword){
            return false;
        }

        $save['loginTime'] = time();
        $save['loginIp'] = getClientIp();
        $save['loginCount'] = ['inc', 1];
        $save['token'] = md5($save['loginTime'] . $param['account'] . rand(100, 999));
        try{
            (new AdminModel())->save($save, ['aid' => $checkPassword['aid']]);
        }catch (Exception $e){
            Log::error("login error:" . $e->getMessage());
            return false;
        }
        $response['token'] = $save['token'];
        $response['account'] = $checkPassword['account'];
        return $response;
    }

    public function reset($param = [])
    {
        $save['salt'] = getRandStr();
        $save['password'] = md5($param['repeatPassword'] . $save['salt']);

        $where['account'] = $param['account'];
        try{
            (new AdminModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("reset error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function checkPassword($account = '', $password = '')
    {
        if(!$account || !$password)return false;
        $where['account'] = $account;
        $adminInfo = (new AdminModel())->field('aid, account, password, salt')->where($where)->find();

        if(!$adminInfo){
            return false;
        }
        $adminInfo = $adminInfo->getData();
        if($adminInfo['password'] == md5($password . $adminInfo['salt'])){
            return $adminInfo;
        }else{
            return false;
        }
    }

    public function adminDelete($aid = 0)
    {
        if($aid == 1)return false;
        try{
            $where['aid'] = $aid;
            (new AdminModel())->where($where)->delete();
        }catch (Exception $e){
            Log::error("adminDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function adminInsert($param = [])
    {
        $create['account'] = $param['account'];
        $create['salt'] = getRandStr();
        $create['password'] = md5($param['password'] . $create['salt']);
        $create['remark'] = $param['remark'];
        try{
            (new AdminModel())->create($create);
        }catch (Exception $e){
            Log::error("adminInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function adminUpdate($param = [])
    {
        if($param['aid'] != 1){
            $save['account'] = $param['account'];
        }
        if($param['password']){
            $save['salt'] = getRandStr();
            $save['password'] = md5($param['password'] . $save['salt']);
        }
        $save['remark'] = $param['remark'];
        try{
            (new AdminModel())->save($save, ['aid' => $param['aid']]);
        }catch (Exception $e){
            Log::error("adminUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}