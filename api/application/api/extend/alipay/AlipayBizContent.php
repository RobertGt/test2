<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/1/20 16:32
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend\alipay;


class AlipayBizContent
{
    private $outTradeNo;
    private $subject;
    private $body;
    private $timeoutExpress = '15d';
    private $totalAmount;
    private $productCode = 'QUICK_MSECURITY_PAY';

    public function setOutTradeNo($outTradeNo){
        $this->outTradeNo = $outTradeNo;
    }

    public function getOutTradeNo(){
        return $this->outTradeNo;
    }

    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function getSubject(){
        return $this->subject;
    }

    public function setBody($body){
        $this->body = $body;
    }

    public function getBody(){
        return $this->body;
    }

    public function setTimeoutExpress($timeoutExpress){
        $this->timeoutExpress = $timeoutExpress;
    }

    public function getTimeoutExpress(){
        return $this->timeoutExpress;
    }

    public function setTotalAmount($totalAmount){
        $this->totalAmount = $totalAmount;
    }

    public function getTotalAmount(){
        return $this->totalAmount;
    }

    public function setProductCode($productCode){
        $this->productCode = $productCode;
    }

    public function getProductCode(){
        return $this->productCode;
    }

    public function getBizContent(){
        $bizContent = [
            'out_trade_no' => $this->outTradeNo ,
            'subject' => $this->subject,
            'body'   => $this->body,
            'timeout_express' => $this->timeoutExpress,
            'total_amount' => $this->totalAmount,
            'product_code' => $this->productCode
        ];
        return json_encode($bizContent);
    }
}