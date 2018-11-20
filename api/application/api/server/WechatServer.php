<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 23:01
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\UserModel;
use think\Config;
use think\Log;

class WechatServer
{
    private $access_token = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    private $user_info = 'https://api.weixin.qq.com/sns/userinfo';

    public function login($imei, $code)
    {
        $wechatConfig = Config::get('third_party.wechat');
        $http = new HttpServer();
        $param['appid'] = $wechatConfig['appid'];
        $param['secret'] = $wechatConfig['secret'];
        $param['code'] = $code;
        $param['grant_type'] = 'authorization_code';
        $url = $this->access_token . '?' . http_build_query($param);
        $resp = $http->request($url);
        if($resp && $token = json_decode($resp, true)){
            if(!empty($token['access_token']) && !empty($token['openid'])){
                $userInfo['access_token'] = $token['access_token'];
                $userInfo['openid'] = $token['openid'];
                $url = $this->user_info . '?' . http_build_query($userInfo);
                $resp = $http->request($url);
                if($resp && $user = json_decode($resp, true)){
                    if(!empty($resp['openid'])){
                        $create['nickname'] = $resp['nickname'];
                        $create['avatar'] = $resp['headimgurl'];
                        $create['sex'] = $resp['sex'];
                        $create['province'] = $resp['province'];
                        $create['city'] = $resp['city'];
                        $create['platform'] = 'wechat';
                        $create['openId'] = $resp['openid'];
                        $userid = (new UserModel())->register($create, $imei);
                        if($userid){
                            $create['uid'] = authcode($userid, 'ENCODE');
                            return $create;
                        }
                    }else{
                        Log::error('用户获取解析失败:' . print_r($resp, true));
                    }
                }else{
                    Log::error('获取微信用户详情失败');
                }
            }else{
                Log::error('Token 解析失败:' . print_r($token, true));
            }
        }else{
            Log::error('获取微信Token失败');
        }
        return false;
    }
}