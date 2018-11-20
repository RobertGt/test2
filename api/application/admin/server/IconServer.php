<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:55
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\IconModel;
use think\Exception;
use think\Log;

class IconServer
{
    public function iconList($param = [])
    {
        if(!empty($param['seach']['name'])){
            $where['name'] = ['like', '%' . $param['seach']['name'] . '%'];
        }
        $where['isDelete'] = 0;

        $themeModel = new IconModel();
        $total = $themeModel->where($where)->count();
        $list = $themeModel->where($where)->field('iconId id, name, iconUrl, createTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("sort desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['iconUrl'] = urlCompletion($info['iconUrl']);
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function iconInsert($param = [])
    {
        $create['name'] = $param['name'];
        $create['iconUrl'] = $param['iconUrl'];
        try{
            (new IconModel())->create($create);
        }catch (Exception $e){
            Log::error("iconInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function iconInfo($iconId = 0)
    {
        $where['iconId'] = $iconId;
        $where['isDelete'] = 0;

        $info = (new IconModel())->field('iconId, name, iconUrl, createTime')->where($where)->find();

        if($info){
            $info = $info->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['viewUrl'] = urlCompletion($info['iconUrl']);
        }else{
            $info = [];
        }
        return $info;
    }

    public function iconUpdate($param = [])
    {
        $save['name'] = $param['name'];
        $save['iconUrl'] = $param['iconUrl'];
        try{
            (new IconModel())->save($save, ['iconId' => $param['iconId']]);
        }catch (Exception $e){
            Log::error("iconUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function iconDelete($iconId = 0)
    {
        try{
            $save['isDelete'] = 1;
            $where['iconId'] = $iconId;
            (new IconModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("iconDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}