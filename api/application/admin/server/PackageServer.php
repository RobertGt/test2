<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 12:07
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\PackageModel;
use app\admin\model\PayModel;
use app\admin\model\TempletModel;
use app\admin\model\UserModel;
use app\api\model\UserMessageModel;
use think\Exception;
use think\Log;

class PackageServer
{
    public function packageInfo($id = 0)
    {
        $where['packageId'] = $id;

        $packageInfo = (new PackageModel())->field('packageId, packageName, packageType, upload, download, price')->where($where)->find();

        if($packageInfo){
            $packageInfo = $packageInfo->getData();
            $packageInfo['price'] = sprintf("%.2f", $packageInfo['price'] / 100);
        }else{
            $packageInfo = [];
        }
        return $packageInfo;
    }

    public function packageList($param = [])
    {

        $where['isDelete'] = 0;
        if(!empty($param['seach']['packageName'])){
            $where['packageName'] = ['like', '%' . $param['seach']['packageName'] . '%'];
        }
        $packageModel = new PackageModel();
        $total = $packageModel->where($where)->count();
        $list = $packageModel
            ->alias('p')
            ->where($where)
            ->field('p.packageId id, p.packageName, p.packageType, p.upload, p.download, p.price, p.createTime, (select count(*) from bas_user_renewals s where s.packageId = p.packageId and state = 1) number')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['package'] = $info['packageType'] == 1 ? "<span class=\"label label-primary\">下载点数 : " . $info['download'] . "</span>":
                        "<span class=\"label label-primary\">上传数量 : " . $info['upload'] . "</span>  <span class=\"label label-primary\">每日下载 : ". $info['download'] . "</span>";

            $info['packageType'] = $info['packageType'] == 1 ? "增值套餐" : "综合套餐";
            $info['price'] = sprintf("%.2f", $info['price'] / 100);
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function packageDelete($id)
    {
        try{
            $save['isDelete'] = 1;
            $where['packageId'] = $id;
            (new PackageModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("packageDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function packageInsert($param = [])
    {
        $create['packageName'] = $param['packageName'];
        $create['packageType'] = $param['packageType'];
        $create['upload'] = $param['packageType'] ? 0 : $param['upload'];
        $create['download'] = $param['download'];
        $create['price'] = intval($param['price'] * 100);
        try{
            (new PackageModel())->create($create);
            if($param['message'] == 1){
                $templet = (new TempletModel())->where(['templetType' => 2, 'state' => 0])->find();
                if($templet){
                    $users = (new UserModel())->where(['state' => 0])->field('uid')->select();
                    $message = [];
                    foreach ($users as $k => $v){
                        $c['uid'] = $v['uid'];
                        $c['title'] = $templet['title'];
                        $c['type'] = 2;
                        $c['message'] = str_replace("{package}", $param['packageName'], $templet['message']);
                        $message[] = $c;
                    }
                    if($message)(new UserMessageModel())->saveAll($message);
                }
            }
        }catch (Exception $e){
            Log::error("packageInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function packageUpdate($param = [])
    {
        $save['packageName'] = $param['packageName'];
        $save['packageType'] = $param['packageType'];
        $save['upload'] = $param['packageType'] ? 0 : $param['upload'];
        $save['download'] = $param['download'];
        $save['price'] = intval($param['price'] * 100);

        try{
            (new PackageModel())->save($save, ['packageId' => $param['id']]);
        }catch (Exception $e){
            Log::error("packageUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function payInfo()
    {
        $field = 'wechat, secret, mchid, apikey, alipay, privateKey, publicKey';
        $payInfo = (new PayModel())->field($field)->where(['payId' => 1])->find();
        if($payInfo){
            $payInfo = $payInfo->getData();
        }else{
            $field = explode(',', $field);
            foreach ($field as $v){
                $payInfo[$v] = '';
            }
        }
        return $payInfo;
    }

    public function payUpdate($param)
    {
        $param['payId'] = 1;
        try{
            (new PayModel())->update($param);
        }catch (Exception $e){
            Log::error("payUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}