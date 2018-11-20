<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:38
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class ThemeValidate extends Validate
{
    protected $rule = [
        'themeId'        => 'require',
        'taskColour'     => 'require|max:7',
        'restColour'     => 'require|min:7',
    ];

    protected $scene = [
        'insert'       =>  ['taskColour', 'restColour'],
        'update'       =>  ['themeId', 'taskColour', 'restColour'],
        'checkId'      =>  ['themeId'],
    ];
}