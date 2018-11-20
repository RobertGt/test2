<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/13 0:11
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class TaskModel extends Model
{
    protected $table = 'bas_task';
    protected $pk = 'taskId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
    protected $iconModel = 'bas_icon';
    protected $ringModel = 'bas_ring';

    public function taskList($imei = '', $pageNum = 1, $pageSize = 10)
    {
        $where['t.isDelete'] = 0;
        if($imei){
            if(is_array($imei)){
                $where['t.imei'] = ['in', $imei];
            }else{
                $where['t.imei'] = $imei;
            }
        }
        $task = $this->alias('t')
                    ->join($this->iconModel . ' i' , 't.iconId = i.iconId' , 'LEFT')
                    ->join($this->ringModel . ' r' , 't.ringId = r.ringId' , 'LEFT')
                    ->field('t.taskId, t.taskName, t.colour, t.taskType, t.minute, r.ringUrl, i.iconUrl')
                    ->where($where)
                    ->page($pageNum, $pageSize)
                    ->order('t.sort DESC, t.updateTime DESC')
                    ->select();
        return $task;
    }

    public function taskInfo($where = []){
        $task = $this->alias('t')
            ->join($this->iconModel . ' i' , 't.iconId = i.iconId' , 'LEFT')
            ->join($this->ringModel . ' r' , 't.ringId = r.ringId' , 'LEFT')
            ->field('t.taskId, t.taskName, t.colour, t.taskType, t.minute, r.ringUrl, i.iconUrl')
            ->where($where)
            ->order('t.sort DESC, t.updateTime DESC')
            ->find();
        return $task;
    }

    public function imeiTaskListByTaskId($taskId)
    {
        $table = $this->table;
        $taskList = $this->field('taskId')->where('imei', 'IN', function ($query) use ($taskId, $table) {
                        $query->table($table)->where(['taskId' => $taskId])->field('imei');
                    })->where('isDelete', 0)->order('sort DESC, updateTime DESC')->select();

        return $taskList;
    }
}