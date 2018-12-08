<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/1/23 9:43
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend\wechat;


class WechatTradeNativePayRequest
{
    private $body;
    private $outTradeNo;
    private $totalFee;
    private $spbillCreateIp;
    private $notifyUrl;
    private $tradeType = 'NATIVE';

    public function setBody($body){
        $this->body = $body;
    }

    public function getBody(){
        return $this->body;
    }

    public function setOutTradeNo($outTradeNo){
        $this->outTradeNo = $outTradeNo;
    }

    public function getOutTradeNo(){
        return $this->outTradeNo;
    }

    public function setTotalFee($totalFee){
        $this->totalFee = $totalFee;
    }

    public function getTotalFee(){
        return $this->totalFee;
    }

    public function setSpbillCreateIp($spbillCreateIp){
        $this->spbillCreateIp = $spbillCreateIp;
    }

    public function getSpbillCreateIp(){
        return $this->spbillCreateIp;
    }

    public function setNotifyUrl($notifyUrl){
        $this->notifyUrl = $notifyUrl;
    }

    public function getNotifyUrl(){
        return $this->notifyUrl;
    }

    public function setTradeType($tradeType){
        $this->tradeType = $tradeType;
    }

    public function getTradeType(){
        return $this->tradeType;
    }
}