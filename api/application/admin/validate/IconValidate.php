<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:56
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class IconValidate extends Validate
{
    protected $rule = [
        'iconId'         => 'require',
        'name'           => 'checkIconName',
        'iconUrl'        => 'require',
    ];

    protected $message = [
        "iconUrl"        => '请上传ICON',
    ];

    protected $scene = [
        'insert'       =>  ['name', 'iconUrl'],
        'update'       =>  ['iconId', 'name', 'iconUrl'],
        'checkId'      =>  ['iconId'],
    ];

    public function checkIconName($iconName, $rule, $data){
        if(!$iconName){
            return '名称不能为空';
        }

        if(mb_strlen($iconName, 'utf8') > 10){
            return '名称不得超过10位字符';
        }

        return true;
    }
}