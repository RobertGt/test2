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
        $packageInfo = $packageModel->where(['packageId' => $userInfo['packageId']])->field('packageId, packageName, price, day')->find();
        if($packageInfo){
            $response['packageName'] = $packageInfo['packageName'];
            if($userInfo['expireTime'] && $userInfo['expireTime'] > $now){
                $dayPrice = round($packageInfo['price'] / $packageInfo['day'], 2);
                $userPrice = $packageInfo['price'];
                $day = floor(($userInfo['expireTime'] - $now) / 86400);
                $price = $dayPrice * $day;
            }
        }
        $packageList = $packageModel->field('packageId, packageName, packageType, upload, download, price, 1 num')->where(['isDelete' => 0])->order('price asc')->select();
        $packages = [];
        foreach ($packageList as $value){
            $info = $value->getData();
            $info['package'] = $info['packageType'] == 1 ? "付费下载点数" . $info['download'] . '次':
                "最大可上传".  $info['upload'] ."个应用,  每天可下载".  $info['download'] ."次";
            $info['deduction'] = 0;
            if($info['packageType'] == 1){
                $info['price'] = sprintf("%.2f", $info['price'] / 100);
                $packages[] = $info;
            }elseif ($info['packageId'] == $userInfo['packageId']){
                $info['price'] = sprintf("%.2f", $info['price'] / 100);
                $packages[] = $info;
            }elseif ($info['price'] >= $userPrice){
                $info['num'] = ceil($day / 31) ? ceil($day / 31) : 1;
                $info['price'] = sprintf("%.2f", $info['price'] / 100);
                $info['deduction'] = sprintf("%.2f", $price / 100);
                $packages[] = $info;
            }else{
                continue;
            }
        }
        $response['packages'] = $packages;
        return $response;
    }

    public function buyPackage($packageInfo = [], $param, $orderNum)
    {
        $create['orderNum'] = $orderNum;
        $create['uid'] = $param['uid'];
        $create['oldPackage'] = $param['userPackage'];
        $create['packageId'] = $packageInfo['packageId'];
        $create['original'] = $packageInfo['price'] * 100 * $param['num'];
        $create['price']    = $create['original'] - $packageInfo['deduction'] * 100;
        if($create['price'] <= 0)$create['price'] = 1;
        $create['deductible'] =  $packageInfo['deduction'];
        $create['package'] = serialize($packageInfo);
        $create['number'] = $param['num'];
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

    public function checkOrderState($orderNum, $uid)
    {
        $state = (new UserRenewalsModel())->where(['uid' => $uid, 'orderNum' => $orderNum, 'state' => 1])->count();
        if($state){
            return true;
        }else{
            return false;
        }
    }
}