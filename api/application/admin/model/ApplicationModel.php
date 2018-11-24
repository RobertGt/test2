<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 17:33
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class ApplicationModel extends Model
{
    protected $table = 'bas_application';
    protected $pk = 'appId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}