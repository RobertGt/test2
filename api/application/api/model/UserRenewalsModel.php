<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/9 0:23
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class UserRenewalsModel extends Model
{
    protected $table = 'bas_user_renewals';
    protected $pk = 'recId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = '';
}