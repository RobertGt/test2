<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/14 22:04
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use think\Validate;

class StatisticValidate extends Validate
{
    protected $rule = [
        'imei'        => 'require',
        'dateType'    => 'require|in:0,1,2',
        'pastTime'    => 'require|in:0,1'
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'checkImei'        =>  ['imei'],
        'checkDateType'    =>  ['imei', 'dateType'],
        'taskDistribution' =>  ['imei', 'dateType', 'pastTime']
    ];
}