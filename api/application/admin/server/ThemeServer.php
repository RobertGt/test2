<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:28
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\ThemeModel;
use think\Exception;
use think\Log;

class ThemeServer
{
    public function ThemeList($param = [])
    {
        $where['isDelete'] = 0;

        $themeModel = new ThemeModel();
        $total = $themeModel->where($where)->count();
        $list = $themeModel->where($where)->field('themeId id, taskColour, restColour, createTime')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("sort desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function themeInsert($param = [])
    {
        $create['taskColour'] = $param['taskColour'];
        $create['restColour'] = $param['restColour'];
        try{
            (new ThemeModel())->create($create);
        }catch (Exception $e){
            Log::error("themeInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function themeInfo($themeId = 0)
    {
        $where['themeId'] = $themeId;
        $where['isDelete'] = 0;

        $info = (new ThemeModel())->field('themeId, taskColour, restColour, createTime')->where($where)->find();

        if($info){
            $info = $info->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
        }else{
            $info = [];
        }
        return $info;
    }

    public function themeUpdate($param = [])
    {
        $save['taskColour'] = $param['taskColour'];
        $save['restColour'] = $param['restColour'];
        try{
            (new ThemeModel())->save($save, ['themeId' => $param['themeId']]);
        }catch (Exception $e){
            Log::error("themeUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function themeDelete($themeId = 0)
    {
        try{
            $save['isDelete'] = 1;
            $where['themeId'] = $themeId;
            (new ThemeModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("themeDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}