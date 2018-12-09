<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 18:51
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\TempletModel;
use app\admin\model\UserModel;
use app\api\model\UserDevicesModel;
use app\api\model\UserMessageModel;
use think\Cache;
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
        $response['realname'] = Cache::get('realName', 1);
        return $response;
    }

    public function userStateUpdate($uid = 0, $state = 0)
    {
        $userModel = new UserModel();
        try{
            $where['uid'] = $uid;
            $userModel->save(['state' => $state], $where);
            $userModel->commit();
        }catch (Exception $e){
            Log::error("userStateUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function userRealnameUpdate($uid = 0, $realname = 0)
    {
        $userModel = new UserModel();
        try{
            $where['uid'] = $uid;
            $userModel->save(['realname' => $realname], $where);
            if($realname == 1){
                $templet = (new TempletModel())->where(['templetType' => 0, 'state' => 0])->find();
                if($templet){
                    $create['uid'] = $uid;
                    $create['title'] = $templet['title'];
                    $create['type'] = 0;
                    $create['message'] = $templet['message'];
                    (new UserMessageModel())->create($create);
                }
            }
        }catch (Exception $e){
            Log::error("userRealnameUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function userPasswordUpdate($uid = 0, $password = 0)
    {
        $userModel = new UserModel();
        try{
            $where['uid'] = $uid;
            $save['salt'] = getRandStr();
            $save['password'] = md5($password . $save['salt']);
            $userModel->save($save, $where);
        }catch (Exception $e){
            Log::error("userPasswordUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function userInfo($uid = 0)
    {
        $where['u.uid'] = $uid;

        $userInfo = (new UserModel())->alias('u')
            ->join('bas_package p', 'u.packageId = p.packageId', 'LEFT')
            ->where($where)
            ->field('u.uid id, u.email, u.mobile, u.state, u.realname, u.packageId, p.packageName, u.surplus, 
                        u.createTime, u.loginTime, u.identityCard, u.upload, u.download, u.surplus, u.wechat, u.imNumber, u.company, u.job, u.expireTime')
            ->find();

        if($userInfo){
            $userInfo = $userInfo->getData();
            $userInfo['wechat'] = $userInfo['wechat'] ? $userInfo['wechat'] : '未填写';
            $userInfo['imNumber'] = $userInfo['imNumber'] ? $userInfo['imNumber'] : '未填写';
            $userInfo['company'] = $userInfo['company'] ? $userInfo['company'] : '未填写';
            $userInfo['job'] = $userInfo['job'] ? $userInfo['job'] : '未填写';
            $userInfo['mobile'] = $userInfo['mobile'] ? $userInfo['mobile'] : '未绑定';
            $userInfo['expireTime'] = $userInfo['expireTime'] ? date('Y-m-d H:i:s', $userInfo['expireTime']) : '';
            $userInfo['realnameText'] = $userInfo['realname'] == 1 ? "审核通过" :
                ($userInfo['realname'] == 2 ? "审核失败" : "待审核");
            $identityCard = $userInfo['identityCard'] ? explode(',', $userInfo['identityCard']) : [];
            $userInfo['image1'] = !empty($identityCard[0]) ? urlCompletion($identityCard[0]) : '../../static/img/timg.png';
            $userInfo['image2'] = !empty($identityCard[1]) ? urlCompletion($identityCard[1]) : '../../static/img/timg.png';
            $userInfo['image3'] = !empty($identityCard[2]) ? urlCompletion($identityCard[2]) : '../../static/img/timg.png';
            //!empty($identityCard[0]) && !empty($identityCard[1]) && !empty($identityCard[2]) &&
            if($userInfo['realname'] == 0){
                $userInfo['real'] = 1;
            }else{
                $userInfo['real'] = 0;
            }
        }else{
            $userInfo = [];
        }
        return $userInfo;
    }
}