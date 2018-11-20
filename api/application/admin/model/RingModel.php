<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 18:15
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class RingModel extends Model
{
    protected $table = 'bas_ring';
    protected $pk = 'ringId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}