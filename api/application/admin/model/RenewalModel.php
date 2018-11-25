<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 2:46
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class RenewalModel extends Model
{
    protected $table = 'bas_user_renewals';
    protected $pk = 'recId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = '';
}