<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/1/23 9:18
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend\wechat;

use app\api\extend\HttpClient;
use think\Config;

class WeChat
{
    //应用ID
    public $appId;

    //商户号
    public $mchId;

    //私钥值
    public $apiKey;

    //
    public $secret;

    //网关
    public $gatewayUrl = "https://api.mch.weixin.qq.com/pay/unifiedorder";

    //签名类型
    public $signType = "MD5";

    public function __construct($config = [])
    {
        foreach ($config as $k => $v){
            $this->{$k} = !$this->checkEmpty($v) ? $v : '';
        }
    }

    public function orderPay($order = [] , $type = 'native'){
        $notifyUrl = Config::get('pay.wechatNotifyUrl');
        switch ($type){
            case 'app':
                $request = new WechatTradeAppPayRequest();
                break;
            case 'native':
                $request = new WechatTradeNativePayRequest();
                break;
            default:
                return false;
        }
        if(isset($order['orderNum']))$request->setOutTradeNo($order['orderNum']);
        if(isset($order['title']))$request->setBody($order['title']);
        if(isset($order['price']))$request->setTotalFee($order['price']);
        $request->setSpbillCreateIp($_SERVER['REMOTE_ADDR']);
        $request->setNotifyUrl($notifyUrl);
        $response = $this->{$type.'Execute'}($request);
        return ['wechat' => $response];
    }

    public function tradeRefund($order = []){
        $params['appid'] = $this->appId;
        $params['mch_id'] = $this->mchId;
        $params['nonce_str'] = getRandStr(32);
        $params['sign_type'] = $this->signType;
        $params['transaction_id'] = $order['transaction'];
        $params['out_refund_no'] = $order['orderNum'];
        $params['total_fee'] = $order['price'];
        $params['refund_fee'] = $order['price'];
        $params['sign'] = $this->generateSign($params);
        $xml = $this->toXml($params);
        $httpClient = new HttpClient();
        $response = $httpClient->Request($this->gatewayUrl , 'POST' , $xml , true , dirname(__FILE__));
        $response = $this->fromXml($response);
        if(!$response){
            return ['code' => 1 , 'reason' => '网络请求错误'];
        }
        if($response['return_code'] == 'FAIL'){
            return ['code' => 1 , 'reason' => empty($response['return_msg']) ? '未知错误' : $response['return_msg']];
        }
        if($response['result_code'] == 'FAIL'){
            return ['code' => 1 , 'reason' => empty($response['err_code_des']) ? '未知错误' : $response['err_code_des']];
        }
        return ['code' => 0 , 'reason' => 'success'];
    }

    public function appExecute($request){
        $params['appid'] = $this->appId;
        $params['mch_id'] = $this->mchId;
        $params['nonce_str'] = getRandStr(32);
        $params['sign_type'] = $this->signType;
        $params['body'] = $request->getBody();
        $params['out_trade_no'] = $request->getOutTradeNo();
        $params['total_fee'] = $request->getTotalFee();
        $params['spbill_create_ip'] = $request->getSpbillCreateIp();
        $params['trade_type'] = $request->getTradeType();

        if ($notify_url = $request->getNotifyUrl()) {
            $params['notify_url'] = $notify_url;
        }
        $params['sign'] = $this->generateSign($params);

        $xml = $this->toXml($params);
        $httpClient = new HttpClient();
        $response = $httpClient->Request($this->gatewayUrl , 'POST' , $xml);
        $response = $this->fromXml($response);
        if(!$response){
            return '请求失败';
        }
        if($response['return_code'] == 'FAIL'){
            return $response['return_msg'];
        }
        if($response['result_code'] == 'FAIL'){
            return $response['err_code_des'];
        }
        return $this->getAppApiParameters($response);
    }

    public function nativeExecute($request)
    {
        $params['appid'] = $this->appId;
        $params['mch_id'] = $this->mchId;
        $params['nonce_str'] = getRandStr(32);
        $params['sign_type'] = $this->signType;
        $params['body'] = $request->getBody();
        $params['out_trade_no'] = $request->getOutTradeNo();
        $params['total_fee'] = $request->getTotalFee();
        $params['spbill_create_ip'] = $request->getSpbillCreateIp();
        $params['trade_type'] = $request->getTradeType();

        if ($notify_url = $request->getNotifyUrl()) {
            $params['notify_url'] = $notify_url;
        }
        $params['sign'] = $this->generateSign($params);

        $xml = $this->toXml($params);
        $httpClient = new HttpClient();
        $response = $httpClient->Request($this->gatewayUrl , 'POST' , $xml);
        $response = $this->fromXml($response);
        if(!$response){
            return '请求失败';
        }
        if($response['return_code'] == 'FAIL'){
            return $response['return_msg'];
        }
        if($response['result_code'] == 'FAIL'){
            return $response['err_code_des'];
        }
        return $response;
    }

    public function getAppApiParameters($res){
        $params['appid'] = $this->appId;
        $timeStamp = time();
        $params['timestamp'] = "$timeStamp";
        $params['partnerid'] = $this->mchId;
        $params['prepayid'] = $res['prepay_id'];
        $params['package'] = 'Sign=WXPay';
        $params['noncestr'] = getRandStr(32);
        $params['sign'] = $this->generateSign($params);
        return $params;
    }

    public function generateSign($params) {
        return $this->signMD5($this->getSignContent($params));
    }

    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    public function md5CheckV1($xml){
        $params = $this->fromXml($xml);
        if($params){
            $sign = $this->generateSign($params);
            if($sign == $params['sign']){
                if($params['return_code'] == 'SUCCESS'){
                    if($params['result_code'] == 'SUCCESS'){
                        return $params;
                    }
                }
            }
        }
        return false;
    }

    protected function signMD5($sign) {
        if(!$this->apiKey)return false;
        $sign = $sign . "&key=" . $this->apiKey;
        $sign = MD5($sign);
        $sign = strtoupper($sign);
        return $sign;
    }

    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    public function toXml($params)
    {
        $xml = "<xml>";
        foreach ($params as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function fromXml($xml)
    {
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}