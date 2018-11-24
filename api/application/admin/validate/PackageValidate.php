<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 12:06
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class PackageValidate extends Validate
{
    protected $rule = [
        'id'            => 'require',
        'packageName'   => 'require',
        'upload'        => 'integer',
        'download'      => 'integer',
        'price'         => 'number',
    ];

    protected $scene = [
        'checkId'      =>  ['id'],
        'insert'       =>  ['packageName', 'upload', 'download', 'price'],
        'update'       =>  ['id', 'packageName', 'upload', 'download', 'price'],
    ];
}