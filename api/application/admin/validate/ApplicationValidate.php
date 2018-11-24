<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 17:54
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class ApplicationValidate extends Validate
{
    protected $rule = [
        'id'            => 'require'
    ];

    protected $scene = [
        'checkId'        =>  ['id']
    ];
}