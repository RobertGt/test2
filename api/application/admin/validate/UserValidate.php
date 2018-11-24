<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 14:26
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'id'            => 'require',
        'password'      => 'require|min:6'
    ];

    protected $scene = [
        'checkId'        =>  ['id'],
        'passwordUpdate' => ['id', 'password']
    ];
}