<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/9 3:59
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class UserMessageModel extends Model
{
    protected $table = 'bas_user_message';
    protected $pk = 'templetId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = '';
}