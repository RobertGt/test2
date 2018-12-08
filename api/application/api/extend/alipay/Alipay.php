<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/1/20 14:58
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend\alipay;


use app\api\extend\HttpClient;
use think\Config;

class Alipay
{
    //应用ID
    public $appId;

    //私钥值
    public $rsaPrivateKey;

    //网关
    public $gatewayUrl = "https://openapi.alipay.com/gateway.do";
    //返回数据格式
    public $format = "json";
    //api版本
    public $apiVersion = "1.0";

    // 表单提交字符集编码
    public $postCharset = "UTF-8";

    //支付宝公钥
    public $alipayrsaPublicKey;

    private $fileCharset = "UTF-8";

    //签名类型
    public $signType = "RSA2";

    public $encryptType = "AES";


    public function __construct($config = [])
    {
        foreach ($config as $k => $v){
            $this->{$k} = !$this->checkEmpty($v) ? $v : '';
        }
    }

    public function orderPay($order = [] , $type = 'app'){
        $notifyUrl = Config::get('pay.alipayNotifyUrl');
        switch ($type){
            case 'app':
                $request = new AlipayTradeAppPayRequest();
                break;
            case 'page':
                $request = new AlipayTradePagePayRequest();
                break;
            default:
                return false;
        }
        $bizContent = new AlipayBizContent();
        if(isset($order['orderNum']))$bizContent->setOutTradeNo($order['orderNum']);
        if(isset($order['title']))$bizContent->setSubject($order['title']);
        if(isset($order['title']))$bizContent->setBody($order['title']);
        if(isset($order['price']))$bizContent->setTotalAmount($order['price'] / 100);
        $bizContent = $bizContent->getBizContent();
        $request->setBizContent($bizContent);
        $request->setNotifyUrl($notifyUrl);
        $response = $this->{$type.'Execute'}($request);
        return ['alipay' => $response];
    }

    public function appExecute($request){
        $this->setupCharsets($request);
        $params['app_id'] = $this->appId;
        $params['method'] = $request->getApiMethodName();
        $params['format'] = $this->format;
        $params['sign_type'] = $this->signType;
        $params['timestamp'] = date("Y-m-d H:i:s");
        $params['charset'] = $this->postCharset;

        $version = $request->getApiVersion();
        $params['version'] = $this->checkEmpty($version) ? $this->apiVersion : $version;

        if ($notify_url = $request->getNotifyUrl()) {
            $params['notify_url'] = $notify_url;
        }
        $params['biz_content'] = $request->getBizContent();

        ksort($params);

        $params['sign'] = $this->generateSign($params, $this->signType);

        foreach ($params as &$value) {
            $value = $this->characet($value, $params['charset']);
        }

        return http_build_query($params);
    }

    public function tradeRefund($order = []){
        $request = new AlipayTradeRefundRequest();
        $bizContent = new AlipayRefundBizContent();
        if(isset($order['transaction']))$bizContent->setTradeNo($order['transaction']);
        if(isset($order['orderNum']))$bizContent->setOutTradeNo($order['orderNum']);
        if(isset($order['title']))$bizContent->setRefundReason($order['title'] . '(订单退款)');
        if(isset($order['price']))$bizContent->setRefundAmount($order['price'] / 100);
        $bizContent = $bizContent->getBizContent();
        $request->setBizContent($bizContent);
        $response = $this->httpExecute($request);
        return $response;
    }

    public function httpExecute($request){
        $this->setupCharsets($request);
        $params['app_id'] = $this->appId;
        $params['method'] = $request->getApiMethodName();
        $params['format'] = $this->format;
        $params['sign_type'] = $this->signType;
        $params['timestamp'] = date("Y-m-d H:i:s");
        $params['charset'] = $this->postCharset;

        $version = $request->getApiVersion();
        $params['version'] = $this->checkEmpty($version) ? $this->apiVersion : $version;

        $params['biz_content'] = $request->getBizContent();

        ksort($params);

        $params['sign'] = $this->generateSign($params, $this->signType);

        foreach ($params as &$value) {
            $value = $this->characet($value, $params['charset']);
        }

        $http = new HttpClient();
        $http->setHeader('Content-Type' , 'application/x-www-form-urlencoded;charset=' . $this->postCharset);
        $response = $http->Request($this->gatewayUrl , 'POST' , http_build_query($params));
        $response = json_decode($response , true);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(!$response){
            return ['code' => 1 , 'reason' => '网络请求错误'];
        }
        if(!empty($response[$responseNode]['code']) && $response[$responseNode]['code'] == 10000){
            return ['code' => 0 , 'reason' => 'success'];
        }else{
            return ['code' => 1 , 'reason' => empty($response[$responseNode]['msg']) ? '未知错误' : $response['alipay_trade_refund_response']['msg']];
        }
    }

    /*
		页面提交执行方法
		@param：跳转类接口的request; $httpmethod 提交方式。两个值可选：post、get
		@return：构建好的、签名后的最终跳转URL（GET）或String形式的form（POST）
		auther:笙默
	*/
    public function pageExecute($request, $httpmethod = "GET") {

        $this->setupCharsets($request);

        //组装系统参数
        $params["app_id"] = $this->appId;
        $params["version"] = $request->getApiVersion();
        $params["format"] = $this->format;
        $params["sign_type"] = $this->signType;

        $params["method"] = $request->getApiMethodName();
        $params["timestamp"] = date("Y-m-d H:i:s");
        $params["prod_code"] = $request->getProdCode();
        $params["notify_url"] = $request->getNotifyUrl();
        $params["return_url"] = $request->getReturnUrl();
        $params["charset"] = $this->postCharset;

        //获取业务参数
        $params['biz_content'] = $request->getBizContent();

        //待签名字符串
        $preSignStr = $this->getSignContent($params);

        //签名
        $params["sign"] = $this->generateSign($params, $this->signType);

        if ("GET" == strtoupper($httpmethod)) {

            $preString=$this->getSignContentUrlencode($params);
            //拼接GET请求串
            $requestUrl = $this->gatewayUrl."?".$preString;
            return $requestUrl;
        } else {
            //拼接表单字符串
            return $this->buildRequestForm($params);
        }
    }



    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    protected function buildRequestForm($para_temp) {

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gatewayUrl."?charset=".trim($this->postCharset)."' method='POST'>";
        foreach ($para_temp as $key => $val){
            if (false === $this->checkEmpty($val)) {
                //$val = $this->characet($val, $this->postCharset);
                $val = str_replace("'","&apos;",$val);
                //$val = str_replace("\"","&quot;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";

        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        echo $sHtml;exit;
        return $sHtml;
    }


    //此方法对value做urlencode
    public function getSignContentUrlencode($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . urlencode($v);
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }

    public function rsaCheckV1($params , $signType = 'RSA') {
        $sign = isset($params['sign']) ? $params['sign'] : '';
        $params['sign_type'] = null;
        $params['sign'] = null;
        return $this->verify($this->getSignContent($params), $sign ,$signType);
    }

    public function getSignContent($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);

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

    protected function sign($data, $signType = "RSA") {
        if(!$this->rsaPrivateKey)return false;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->rsaPrivateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        $sign = base64_encode($sign);
        return $sign;
    }

    public function verify($data, $sign , $signType = 'RSA') {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($this->alipayrsaPublicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }

        return $result;
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

    private function setupCharsets($request) {
        if ($this->checkEmpty($this->postCharset)) {
            $this->postCharset = 'UTF-8';
        }
        $str = preg_match('/[\x80-\xff]/', $this->appId) ? $this->appId : print_r($request, true);
        $this->fileCharset = mb_detect_encoding($str, "UTF-8, GBK") == 'UTF-8' ? 'UTF-8' : 'GBK';
    }

    private function characet($data, $targetCharset){
        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
}