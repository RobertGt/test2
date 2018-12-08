<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/1/20 16:59
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend;

use app\admin\model\PayModel;

class Payment
{
    public $errMsg;
    public function pay($orderInfo){
        $res = false;
        $pay = (new PayModel())->find();
        switch ($orderInfo['payType']){
            //支付宝
            case 0:
                $this->errMsg = '支付方式不支持';
                break;
                /*$config = [
                    'appId'              =>  !empty($pay['alipay']) ? $pay['alipay'] : '',
                    'rsaPrivateKey'      =>  !empty($pay['privateKey']) ? $pay['privateKey'] : '',
                    'alipayrsaPublicKey' =>  !empty($pay['publicKey']) ? $pay['publicKey'] : ''
                ];
                $res = (new \app\api\extend\alipay\Alipay($config))->orderPay($orderInfo , 'page');
                break;*/
            //微信
            case 1:
                $config = [
                    'appId'  =>  !empty($pay['wechat']) ? $pay['wechat'] : '',
                    'mchId'  =>  !empty($pay['mchid']) ? $pay['mchid'] : '',
                    'apiKey' =>  !empty($pay['apikey']) ? $pay['apikey'] : '',
                    'secret' =>  !empty($pay['secret']) ? $pay['secret'] : '',
                ];
                $res = (new \app\api\extend\wechat\WeChat($config))->orderPay($orderInfo);
                if(!is_array($res['wechat'])){
                    $this->errMsg = $res['wechat'];
                }
                break;
            default:
                $this->errMsg = '支付方式不支持';
                break;
        }
        return $res;
    }

}