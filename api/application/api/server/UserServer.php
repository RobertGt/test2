<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/27 2:22
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\UserModel;
use think\Cache;
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
        return $create;
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
        return $user;
    }
}