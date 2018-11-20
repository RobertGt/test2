<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 18:54
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class UserModel extends Model
{
    protected $table = 'bas_user';
    protected $pk = 'uid';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}