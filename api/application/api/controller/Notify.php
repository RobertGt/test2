<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/9 3:31
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\admin\model\PayModel;
use app\api\extend\wechat\WeChat;
use app\api\model\UserModel;
use app\api\model\UserRenewalsModel;
use think\Exception;
use think\Log;
use think\Request;

class Notify
{
    public function wechatNotify(Request $request){
        $result['return_code'] = "FAIL";
        $result['return_msg'] = "处理失败";
        $xml = $request->getInput();
        $pay = (new PayModel())->find();
        $config = [
            'appId'  =>  !empty($pay['wechat']) ? $pay['wechat'] : '',
            'mchId'  =>  !empty($pay['mchid']) ? $pay['mchid'] : '',
            'apiKey' =>  !empty($pay['apikey']) ? $pay['apikey'] : '',
            'secret' =>  !empty($pay['secret']) ? $pay['secret'] : '',
        ];
        $wechat = new WeChat($config);
        $params = $wechat->md5CheckV1($xml);
        if($params){
            $orderNum = !empty($params['out_trade_no']) ? $params['out_trade_no'] : '';
            if(!$orderNum){
                Log::error('wechatNotify out_trade_no is NULL : '.print_r($params , true));
                arrayToXml($result);
            }
            $userRecCharge = new UserRenewalsModel();
            $where['orderNum'] = $orderNum;
            $where['state'] = 0;
            $info = $userRecCharge->where($where)->field('recId , uid , price , packageId, package, state, number')->find();
            if(empty($info)){
                Log::error('wechatNotify userRecCharge not found : '.print_r($params , true));
                arrayToXml($result);
            }
            $package = unserialize($info['package']);
            if($info['price'] != $params['total_fee']){
                Log::error('wechatNotify total_fee not equal : '.print_r($params , true));
                arrayToXml($result);
            }
            $save['state'] = 1;
            $save['tradeNum'] = $params['transaction_id'];
            $save['payTime'] = time();
            $userModel = new UserModel();
            $w['uid'] = $info['uid'];
            $userInfo = $userModel->where($w)->field('uid, packageId, expireTime')->find();
            if(!$userInfo){
                Log::error('wechatNotify userInfo not found : '.print_r($params , true));
                arrayToXml($result);
            }
            $userRecCharge->startTrans();
            try{
                $q1 = $userRecCharge->save($save , $where);
                if($package['packageType'] == 1){
                    $u['surplus'] = ['inc', $package['download']];
                }else{
                    $time = 31 * 60 * 60 * 24;
                    $u['expireTime'] = $userInfo['expireTime'] > time() ?
                        $userInfo['expireTime'] + $info['number']  * $time : time() + $info['number']  * $time;
                    $u['packageId'] = $info['packageId'];
                    $u['upload'] = $package['upload'];
                    $u['download'] = $package['download'];
                }
                $q2 = $userModel->save($u, $w);
                if($q1 && $q2){
                    $userRecCharge->commit();
                    $result['return_code'] = "SUCCESS";
                    $result['return_msg'] = "OK";
                    arrayToXml($result);
                }
                $userRecCharge->rollback();
            }catch (Exception $e){
                $userRecCharge->rollback();
                Log::error('alipayNotify Error : '.$e->getMessage());
                arrayToXml($result);
            }
            arrayToXml($result);
        }else{
            Log::error('wechatNotify md5CheckV1 Error : '.print_r($wechat , true));
            arrayToXml($result);
        }
    }
}