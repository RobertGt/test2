<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/5 23:27
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class ApplicationModel extends Model
{
    protected $table = 'bas_application';
    protected $pk = 'appId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}