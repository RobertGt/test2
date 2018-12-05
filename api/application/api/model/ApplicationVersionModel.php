<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/5 23:51
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class ApplicationVersionModel extends Model
{
    protected $table = 'bas_application_version';
    protected $pk = 'apkId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}