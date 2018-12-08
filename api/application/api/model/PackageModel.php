<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/8 23:04
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class PackageModel extends Model
{
    protected $table = 'bas_package';
    protected $pk = 'packageId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}