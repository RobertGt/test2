<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 1:55
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class TempletModel extends Model
{
    protected $table = 'bas_templet';
    protected $pk = 'templetId';
    protected $autoWriteTimestamp = true;
    protected $createTime = '';
    protected $updateTime = 'updateTime';
}