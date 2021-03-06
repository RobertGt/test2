<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/8 22:57
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\PackageServer;
use think\Request;

class Package extends Base
{
    public function packageList(Request $request)
    {
        $response = (new PackageServer())->packageList($this->userInfo);
        ajax_info(0, 'success', $response);
    }

    public function buyPackage(Request $request)
    {
        $param = [
            'packageId'    => $request->param('packageId', 0, 'intval'),
            'payType'      => $request->param('payType', 0),
            'num'          => $request->param('num', 0),
            'userPackage'  => $this->userInfo['packageId'],
            'uid'          => $this->userInfo['uid'],
        ];
        $packageList = (new PackageServer())->packageList($this->userInfo);
        $packageInfo = [];
        foreach ($packageList['packages'] as $value){
            if($value['packageId'] == $param['packageId']){
                $packageInfo = $value;
                break;
            }
        }
        if(!$packageInfo){
            ajax_info(1, '无法续费此套餐');
        }
        if($packageInfo['num'] > $param['num']){
            ajax_info(1, '续费数量不得少于' . $packageInfo['num']);
        }
        $orderNum = date('YmdHis') . round(1000, 9999);
        $resp = (new PackageServer())->buyPackage($packageInfo, $param, $orderNum);
        if($resp){
            if($param['payType'] == 1){
                if(isset($resp['wechat']['code_url'])){
                    $qrCode = qrCode($resp['wechat']['code_url']);
                }else{
                    ajax_info(1, $resp['wechat']);
                }
            }
            ajax_info(0, 'Success', ['qrCode' => urlCompletion($qrCode), 'orderNum' => $orderNum]);
        }else{
            ajax_info(1, '订单生成失败');
        }
    }

    public function checkOrderState(Request $request)
    {
        $param = [
            'orderNum'     => $request->param('orderNum', ''),
        ];
        if(!$param['orderNum']){
            ajax_info(1, 'error');
        }
        $resp = (new PackageServer())->checkOrderState($param['orderNum'], $this->userInfo['uid']);
        if($resp){
            ajax_info(0, 'success');
        }else{
            ajax_info(1, 'error');
        }
    }
}