<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/13 0:14
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\IconModel;
use app\api\model\TaskFinishModel;
use app\api\model\TaskModel;
use app\api\model\UserDevicesModel;
use think\Exception;
use think\Log;

class TaskServer
{
    public function taskList($param = [])
    {
        if($param['uid']){
            $param['imei'] = $this->getImeiByUid($param['uid']);
        }
        $taskList = (new TaskModel())->taskList($param['imei'], $param['pageNum'], $param['pageSize']);

        $task = [];
        foreach ($taskList as $value){
            $info = $value->getData();
            $info['taskId'] = authcode($info['taskId'], 'ENCODE');
            $info['iconUrl'] = urlCompletion($value['iconUrl']);
            $info['ringUrl'] = urlCompletion($value['ringUrl']);
            $task[] = $info;
        }

        return $task;
    }


    public function taskCreate($param = [])
    {
        try{
            (new TaskModel())->create($param);
        }catch (Exception $e){
            Log::error("taskCreate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function taskDelete($param = [])
    {
        try{
            $where['taskId'] = authcode($param['taskId']);
            $where['imei'] = $param['imei'];
            //(new TaskModel())->save(['isDelete' => 1], $where);
			(new TaskModel())->where($where)->delete();
        }catch (Exception $e){
            Log::error("taskDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function taskUpdate($param = [])
    {
        try{
            $where['taskId'] = authcode($param['taskId']);
            $where['isDelete'] = 0;
            $save['taskName'] = $param['taskName'];
            $save['colour'] = $param['colour'];
            $save['iconId'] = $param['iconId'];
            $save['taskType'] = $param['taskType'];
            $save['minute'] = $param['minute'];
            $save['ringId'] = $param['ringId'];

            (new TaskModel())->save($save , $where);
        }catch (Exception $e){
            Log::error("taskUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function taskIcon()
    {
        $icon = [];
        $taskIcon = (new IconModel())->where('isDelete', 0)->field('iconId, name, iconUrl')->order('sort DESC, createTime DESC')->select();
        foreach ($taskIcon as $value){
            $info = $value->getData();
            $info['iconId'] = $info['iconId'];
            $info['iconUrl'] = urlCompletion($info['iconUrl']);

            $icon[] = $info;
        }
        
        return $icon;
    }

    public function taskUpload($param = [])
    {
        $where['taskId'] = authcode($param['taskId']);
        $task = (new TaskModel())->field('imei')->where($where)->find();
        if(!$task){
            Log::info("taskUpload not find task:" . $where['taskId'] );
            return false;
        }
        $task = $task->getData();
        $create['imei']      = $task['imei'];
        $create['taskId']    = $where['taskId'];
        $create['second']   = $param['second'];
        $create['absorbed']  = $param['absorbed'] ? 1 : 0;
        $create['remark']    = $param['remark'];
		$create['finish']    = $param['finish'];
        $create['taskType']    = $param['taskType'];

        (new TaskFinishModel())->create($create);
        return true;
    }

    public function taskNext($taskId = "", $uid = 0)
    {
        $taskModel = new TaskModel();
        $where['taskId'] = authcode($taskId);

        if($uid){
            $imei = $this->getImeiByUid($uid);
        }
        $task = $taskModel->field('imei, sort, updateTime')->where($where)->find();
        if(!$task){
            Log::info("taskNext not find task:" . $where['taskId'] );
            return [];
        }
        $task = $task->getData();
        $where = [
            't.imei'        => $uid ? ['in', $imei] : $task['imei'],
            't.sort'        => ['elt', $task['sort']],
            't.updateTime'  => ['lt', $task['updateTime']],
            't.isDelete'    => 0
        ];

        $taskInfo = $taskModel->taskInfo($where);

        if(!$taskInfo){
            return [];
        }

        $taskInfo = $taskInfo->getData();
        $taskInfo['taskId'] = authcode($taskInfo['taskId'], 'ENCODE');
        $taskInfo['iconUrl'] = urlCompletion($taskInfo['iconUrl']);
        $taskInfo['ringUrl'] = urlCompletion($taskInfo['ringUrl']);
        return $taskInfo;
    }

    public function taskSort($param = [])
    {
        $taskModel = new TaskModel();

        $taskId = authcode($param['taskId']);

        $afterId = $param['afterId'] ? authcode($param['afterId']) : 0;

        //相等代表没移动
        if($afterId == $taskId){
            return true;
        }

        if($param['uid']){
            $imei = $this->getImeiByUid($param['uid']);
            $taskList = $taskModel->where(['imei' => ['in', $imei]])->field('taskId')->order('sort DESC, updateTime DESC')->select();
        }else{
            //查询IMEI所有的task
            $taskList =  $taskModel->imeiTaskListByTaskId($taskId);
        }

        $task = [];
        $moveKey = 0;
        $afterKey = 0;
        $moveInfo = [];
        $count = count($taskList);
        foreach ($taskList as $key => $value){
            $info = $value->getData();
            //把需要移动的元素拿出来
            if($info['taskId'] == $taskId){
                $moveKey = $key;
                $moveInfo = $taskId;
                continue;
            }
            //获取移动的位置
            if($value['taskId'] == $afterId){
                $afterKey = $key;
            }

            $task[$key] = $info['taskId'];
        }

        if(!$moveKey && !$afterKey && !$moveInfo){
            return true;
        }
        //如果是0，则移到第一,否则在指定位置插入
        if(!$afterId){
            array_unshift($task, $taskId);
        }else{
            array_splice($task, $afterKey + 1, 0, $taskId);
        }

        try{
            $taskModel->startTrans();
            foreach ($task as $value){
                $taskModel->save(['sort' => $count--], ['taskId' => $value]);
            }
            $taskModel->commit();
        }catch (Exception $e){
            $taskModel->rollback();
            Log::error("taskSort error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    private function getImeiByUid($uid = 0)
    {
        $where['uid'] = $uid;
        $row = (new UserDevicesModel())->where($where)->field('imei')->select();
        $imei = [];
        foreach ($row as $value){
            $imei[] = $value->getData('imei');
        }
        return $imei;
    }
}