<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 19:47
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\UserModel;
use think\Config;
use think\Log;

class QQServer
{
    private $get_user_info = 'https://graph.qq.com/user/get_user_info';

    public function login($imei, $accessToken, $openid)
    {
        $qqConfig = Config::get('third_party.qq');
        $http = new HttpServer();
        $param['oauth_consumer_key'] = $qqConfig['appid'];
        $param['access_token'] = $accessToken;
        $param['openid'] = $openid;
        $url = $this->get_user_info . '?' . http_build_query($param);
        $resp = $http->request($url);
        if($resp && $user = json_decode($resp, true)){
            if(isset($user['ret']) && $user['ret'] == 0){
                $create['nickname'] = $user['nickname'];
                $create['avatar'] = $user['figureurl_qq_1'];
                $create['sex'] = isset($user['gender']) ? ($user['gender'] == '男' ? 1 : 2) : 0;
                $create['province'] = '';
                $create['city'] = '';
                $create['platform'] = 'qq';
                $create['openId'] = $openid;
                $userid = (new UserModel())->register($create, $imei);
                if($userid){
                    $create['uid'] = authcode($userid, 'ENCODE');
                    return $create;
                }
            }else{
                Log::error('获取QQ用户信息失败:' . print_r($user, true));
            }
        }else{
            Log::error('获取QQ用户信息失败');
        }
        return false;
    }
}