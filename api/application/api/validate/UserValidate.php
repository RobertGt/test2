<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 22:51
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'imei'        => 'require',
        'loginType'   => 'require|in:wechat,qq',
		'openid'      => 'require',
		'avatar'      => 'require',
		'sex'         => 'require',
		'nickname'    => 'require'
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'login'          =>  ['imei', 'loginType', 'openid', 'avatar', 'sex', 'nickname']
    ];
}