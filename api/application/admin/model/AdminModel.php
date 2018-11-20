<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/18 23:57
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class AdminModel extends Model
{
    protected $table = 'sys_admin';
    protected $pk = 'aid';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}