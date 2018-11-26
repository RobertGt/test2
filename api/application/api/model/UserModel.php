<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 23:32
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Exception;
use think\Log;
use think\Model;

class UserModel extends Model
{
    protected $table = 'bas_user';
    protected $pk = 'uid';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}