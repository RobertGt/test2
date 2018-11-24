<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 1:00
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class VersionModel extends Model
{
    protected $table = 'bas_application_version';
    protected $pk = 'apkId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}