<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/16 1:03
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use think\Validate;

class ConfigureValidate extends Validate
{
    protected $rule = [
        'imei'        => 'require',
        'theme'       => 'require|integer',
        'taskFinish'  => 'taskFinish|integer',
        'restFinish'  => 'restFinish|integer',
        'noise'       => 'noise|integer',
        'taskTime'    => 'taskTime|between:1,480',
        'sortRest'    => 'sortRest|between:1,480',
        'longRest'    => 'longRest|between:1,480',
        'taskNum'     => 'taskNum|between:1-90',
        'autoNext'    => 'autoNext|in:0,1',
        'screenOn'    => 'screenOn|in:0,1',
        'shockOn'     => 'shockOn|in:0,1',
        'strict'      => 'strict|in:0,1'
    ];

    protected $scene = [
        'checkSetting'    =>  ['imei']
    ];
}