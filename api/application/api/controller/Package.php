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
            'packageId'    => $this->userInfo['packageId'],
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
        $resp = (new PackageServer())->buyPackage($packageInfo, $param);
        if($resp){
            if($param['payType'] == 1){
                $qrCode = qrCode($resp['wechat']['code_url']);
            }
            ajax_info(0, 'Success', ['qrCode' => urlCompletion($qrCode)]);
        }else{
            ajax_info(1, '订单生成失败');
        }
    }
}