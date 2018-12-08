<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/2/3 14:24
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend\alipay;


class AlipayRefundBizContent
{
    private $outTradeNo;
    private $tradeNo;
    private $refundAmount;
    private $refundReason = '';
    private $outRequestNo = '';

    public function setOutTradeNo($outTradeNo){
        $this->outTradeNo = $outTradeNo;
    }

    public function getOutTradeNo(){
        return $this->outTradeNo;
    }

    public function setTradeNo($tradeNo){
        $this->tradeNo = $tradeNo;
    }

    public function getTradeNo(){
        return $this->tradeNo;
    }

    public function setRefundAmount($refundAmount){
        $this->refundAmount = $refundAmount;
    }

    public function getRefundAmount(){
        return $this->refundAmount;
    }

    public function setRefundReason($refundReason){
        $this->refundReason = $refundReason;
    }

    public function getRefundReason(){
        return $this->refundReason;
    }

    public function setOutRequestNo($outRequestNo){
        $this->outRequestNo = $outRequestNo;
    }

    public function getOutRequestNo(){
        return $this->outRequestNo;
    }

    public function getBizContent(){
        $bizContent = [
            'out_trade_no' => $this->outTradeNo ,
            'trade_no' => $this->tradeNo,
            'refund_amount'   => $this->refundAmount,
            'refund_reason' => $this->refundReason,
            'out_request_no' => $this->outRequestNo,
        ];
        return json_encode($bizContent);
    }
}