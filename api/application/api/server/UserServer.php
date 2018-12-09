<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/27 2:22
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\ApplicationDownModel;
use app\api\model\ApplicationModel;
use app\api\model\UserModel;
use think\Cache;
use think\Config;
use think\Exception;
use think\Log;

class UserServer
{



    public function register($param = [])
    {
        $create['email']    = $param['email'];
        $create['salt']     = getRandStr();
        $create['password'] = md5($param['password'] . $create['salt']);
        $create['wechat']   = $param['wechat'];
        $create['imNumber'] = $param['imNumber'];
        $create['company']  = $param['company'];
        $create['job']      = $param['job'];
        $create['token']    = md5(time() . rand(1000, 9999));
        try{
            (new UserModel())->create($create);
            unset($create['salt']);
            unset($create['password']);
        }catch (Exception $e){
            Log::error("register error:" . $e->getMessage());
            return false;
        }
		$create['realName'] = Cache::get('realName', 1);
        $create['realState'] = 0;
        return $create;
    }

    public function userUpdate($param = [], $uid)
    {
        $save = [];
        foreach ($param as $k => $v){
            if($v){
                $save[$k] = $v;
            }
        }
        if($param['password']){
            $save['salt']     = getRandStr();
            $save['password'] = md5($param['password'] . $save['salt']);
        }

        try{
            if($save)(new UserModel())->save($save, ['uid' => $uid]);
        }catch (Exception $e){
            Log::error("userEdit error:" . $e->getMessage());
            return false;
        }
        return true;
    }
    
    public function find($param = [])
    {
        $save['salt']     = getRandStr();
        $save['password'] = md5($param['password'] . $save['salt']);
        $save['token']     = md5(time() . rand(1000, 9999).$param['email']);
        try{
            (new UserModel())->save($save, ['email' => $param['email']]);
        }catch (Exception $e){
            Log::error("find error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function sendMail($param = [])
    {
        $exp = 60 * 10;
        $code = rand(100000,999999);
        if(!empty($param['find'])){
            $state = Cache::set('find_' . $param['email'], $code, $exp);
        }else{
            $state = Cache::set('reg_' . $param['email'], $code, $exp);
        }
        if($state){
            $content = "您的验证码是：<span style='font-weight: bold;font-size: 18px'>" . $code . "</span>, " . "有效期10分钟。";
            $send = think_send_mail($param['email'], $param['email'], $content, "验证码");
        }
        return $send === true ? true : $send;
    }

    public function sendMailUpdate($uid)
    {
        $userInfo = (new UserModel())->where(['uid' => $uid])->field('email')->find()->toArray();
        $p['find'] = 1;
        $p['email'] = $userInfo['email'];
        return $this->sendMail($p);
    }

    public function emailUpdate($param = [], $uid)
    {
        $key = Cache::get($param['updateKey']);
        Cache::rm($param['updateKey']);
        if($key != $uid){
            return false;
        }
        $save['email']     =  $param['email'];
        try{
            (new UserModel())->save($save, ['uid' => $uid]);
        }catch (Exception $e){
            Log::error("emailUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function login($user = [])
    {
        $save['token']     = md5(time() . rand(1000, 9999).$user['uid']);
        $save['loginTime'] = time();
        try{
            (new UserModel())->save($save, ['uid' => $user['uid']]);
            unset($user['salt']);
            unset($user['password']);
            unset($user['uid']);
            $user['token'] =  $save['token'];
        }catch (Exception $e){
            Log::error("login error:" . $e->getMessage());
            return false;
        }
        $user['realname'] = Cache::get('realName', 1);
        return $user;
    }

    public function real($param = [])
    {
        $identityCard[] = $param['front'];
        $identityCard[] = $param['contrary'];
        $identityCard[] = $param['hand'];
        $save['identityCard'] = implode(',', $identityCard);
        $save['realname'] = 0;
        try{
            (new UserModel())->save($save, ['uid' => $param['uid']]);
        }catch (Exception $e){
            Log::error("real error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function userInfo($uid = 0)
    {
        $where['uid'] = $uid;
        $field = "u.email, u.mobile, u.wechat, u.imNumber, u.company, u.job, u.upload, u.download, u.surplus,
                    u.state, u.realname realState, u.packageId, p.packageName, u.identityCard, u.expireTime";
        $userInfo = (new UserModel())->alias('u')
                    ->join('bas_package p', 'p.packageId = u.packageId', 'LEFT')
                    ->where($where)->field($field)->find();
        if(!$userInfo){
            return false;
        }
        $userInfo = $userInfo->getData();
        $where['createTime'] = ['egt', strtotime(date('Y-m-d'))];
        $upload = (new ApplicationModel())->where($where)->count();
        $w['b.uid'] = $uid;
        $w['d.createTime'] = ['egt', strtotime(date('Y-m-d'))];
        $surplus = (new ApplicationDownModel())->alias('d')
                                                ->join('bas_application b', 'd.appId = b.appId')
                                                ->where($w)
                                                ->count();
        if($userInfo['expireTime'] < time()){
            $userInfo['upload'] = Config::get('default.upload');
            $userInfo['surplus'] = Config::get('default.surplus');
        }
        $userInfo['upload'] = $userInfo['upload'] - $upload;
        $userInfo['surplus'] = $userInfo['surplus'] - $surplus;
        $userInfo['mobile'] = $userInfo['mobile'] ? $userInfo['mobile'] : '';
        $userInfo['wechat'] = $userInfo['wechat'] ? $userInfo['wechat'] : '';
        $userInfo['imNumber'] = $userInfo['imNumber'] ? $userInfo['imNumber'] : '';
        $userInfo['company'] = $userInfo['company'] ? $userInfo['company'] : '';
        $userInfo['job'] = $userInfo['job'] ? $userInfo['job'] : '';
        $userInfo['packageId'] = authcode($userInfo['packageId'], 'ENCODE');
        $userInfo['packageName'] = $userInfo['packageName'] ? $userInfo['packageName'] : '';
        $userInfo['expireTime'] = $userInfo['expireTime'] ? date('Y-m-d H:i', $userInfo['expireTime']) : '';
        $userInfo['realName'] = Cache::get('realName', 1);
        $identityCard = $userInfo['identityCard'] ? explode(',', $userInfo['identityCard']) : [];
        $userInfo['front'] = !empty($identityCard[0]) ? urlCompletion($identityCard[0]) : '';
        $userInfo['contrary'] = !empty($identityCard[1]) ? urlCompletion($identityCard[1]) : '';
        $userInfo['hand'] = !empty($identityCard[2]) ? urlCompletion($identityCard[2]) : '';
        unset($userInfo['identityCard']);

        return $userInfo;
    }
}