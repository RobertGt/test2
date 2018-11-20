<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/12 23:41
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use think\Validate;

class TaskValidate extends Validate
{
    protected $rule = [
        'imei'        => 'require',
        'taskName'    => 'checkTaskName',
        'colour'      => 'require',
        'iconId'      => 'integer',
        'taskId'      => 'checkTaskId',
        "second"      => 'integer|gt:0',
        "taskType"    => 'integer|in:0,1',
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'taskList'       =>  ['imei'],
        'taskCreate'     =>  ['imei', 'taskName', 'colour', 'iconId', 'taskType'],
        'taskDelete'     =>  ['imei', 'taskId'],
        'taskEdit'       =>  ['taskName', 'colour', 'iconId', 'taskId', 'taskType'],
        'taskUpload'     =>  ['taskId', 'second', 'taskType'],
        'taskNext'       =>  ['taskId'],
    ];

    public function checkTaskName($taskName, $rule, $data){
        if(!$taskName){
            return '任务名称不能为空';
        }

        if(mb_strlen($taskName, 'utf8') > 15){
            return '任务名称不得超过15位字符';
        }

        return true;
    }

    public function checkTaskId($taskId, $rule, $data)
    {
        if(!$taskId || !authcode($taskId)){
            return '任务错误';
        }

        return true;
    }
}