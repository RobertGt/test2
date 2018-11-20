<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 18:13
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\RingModel;
use think\Exception;
use think\Log;

class RingServer
{
    public function ringList($param = [])
    {
        if(!empty($param['seach']['ringName'])){
            $where['ringName'] = ['like', '%' . $param['seach']['ringName'] . '%'];
        }
        $where['noise'] = $param['noise'];
        $where['isDelete'] = 0;

        $themeModel = new RingModel();
        $total = $themeModel->where($where)->count();
        $list = $themeModel->where($where)->field('ringId id, ringName, ringUrl, createTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("sort desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['ringUrl'] = urlCompletion($info['ringUrl']);
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function ringInsert($param = [])
    {
        $create['ringName'] = $param['ringName'];
        $create['ringUrl'] = $param['ringUrl'];
        $create['noise'] = $param['noise'];
        try{
            (new RingModel())->create($create);
        }catch (Exception $e){
            Log::error("ringInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function ringInfo($ringId = 0)
    {
        $where['ringId'] = $ringId;
        $where['isDelete'] = 0;

        $info = (new RingModel())->field('ringId, ringName, ringUrl, createTime')->where($where)->find();

        if($info){
            $info = $info->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['viewUrl'] = urlCompletion($info['ringUrl']);
        }else{
            $info = [];
        }
        return $info;
    }

    public function ringUpdate($param = [])
    {
        $save['ringName'] = $param['ringName'];
        $save['ringUrl'] = $param['ringUrl'];
        try{
            (new RingModel())->save($save, ['ringId' => $param['ringId']]);
        }catch (Exception $e){
            Log::error("ringUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function ringDelete($ringId = 0)
    {
        try{
            $save['isDelete'] = 1;
            $where['ringId'] = $ringId;
            (new RingModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("ringDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}