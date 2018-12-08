<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/8 23:01
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\extend\Payment;
use app\api\model\PackageModel;
use app\api\model\UserRenewalsModel;
use think\Exception;
use think\Log;

class PackageServer
{
    public function packageList($userInfo = [])
    {
        $packageModel = new PackageModel();
        $price = 0;
        $day = 0;
        $userPrice = 0;
        $now = time();
        $response['expireTime'] = $userInfo['expireTime'] ? date('Y-m-d H:i:s', $userInfo['expireTime']) : '';
        $response['packageName'] = '';
        if($userInfo['expireTime'] && $userInfo['expireTime'] > $now){
            $packageInfo = $packageModel->where(['packageId' => $userInfo['packageId']])->field('packageId, packageName, price, day')->find();
            if($packageInfo){
                $dayPrice = round($packageInfo['price'] / $packageInfo['day'], 2);
                $response['packageName'] = $packageInfo['packageName'];
                $userPrice = $packageInfo['price'];
                $day = floor(($userInfo['expireTime'] - $now) / 86400);
                $price = $dayPrice * $day;
            }
        }
        $packageList = $packageModel->field('packageId, packageName, packageType, upload, download, price, price payPrice, 1 num')->where(['isDelete' => 0])->order('price asc')->select();
        $packages = [];
        foreach ($packageList as $value){
            $info = $value->getData();
            $info['price'] = sprintf("%.2f", $info['price'] / 100);
            $info['package'] = $info['packageType'] == 1 ? "付费下载点数" . $info['download'] . '次':
                "最大可上传".  $info['upload'] ."个应用,  每天可下载".  $info['download'] ."次";
            if($info['packageType'] == 1){
                $info['payPrice'] = sprintf("%.2f", $info['payPrice'] / 100);
                $packages[] = $info;
            }elseif ($info['packageId'] == $userInfo['packageId']){
                $info['payPrice'] = sprintf("%.2f", $info['payPrice'] / 100);
                $packages[] = $info;
            }elseif ($info['payPrice'] >= $userPrice){
                $info['num'] = ceil($day / 31);
                $info['payPrice'] = sprintf("%.2f", ($info['payPrice'] * $info['num'] - $price) / 100);
                $packages[] = $info;
            }else{
                continue;
            }
        }
        $response['packages'] = $packages;
        return $response;
    }

    public function buyPackage($packageInfo = [], $param)
    {
        $create['orderNum'] = date('YmdHis') . round(1000, 9999);
        $create['uid'] = $param['uid'];
        $create['oldPackage'] = $param['packageId'];
        $create['packageId'] = $packageInfo['packageId'];
        $create['original'] = $packageInfo['price'] * 100;
        $create['price']    = $packageInfo['payPrice'] * 100;
        $create['deductible'] =  $create['price'] -  $create['original'];
        $create['package'] = serialize($packageInfo);
        $create['number'] = $packageInfo['num'];
        $create['payType'] = $param['payType'];
        $renewalsModel = new UserRenewalsModel();
        $payMent = new Payment();

        try{
            $renewalsModel->create($create);
            $create['title'] = $packageInfo['packageName'] . '购买';
            $result = $payMent->pay($create);
        }catch (Exception $e){
            Log::error("buyPackage error:" . $e->getMessage());
            return false;
        }
        return $result;
    }
}