<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 18:21
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class RingValidate extends Validate
{
    protected $rule = [
        'ringId'         => 'require',
        'ringName'       => 'checkRingName',
        'ringUrl'        => 'require',
    ];

    protected $message = [
        "ringUrl"        => '请上传铃声',
    ];

    protected $scene = [
        'insert'       =>  ['ringName', 'ringUrl'],
        'update'       =>  ['ringId', 'ringName', 'ringUrl'],
        'checkId'      =>  ['ringId'],
    ];

    public function checkRingName($ringName, $rule, $data){
        if(!$ringName){
            return '名称不能为空';
        }
        if(mb_strlen($ringName, 'utf8') > 5){
            return '名称不得超过5位字符';
        }

        return true;
    }
}