<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/13 0:59
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class IconModel extends Model
{
    protected $table = 'bas_icon';
    protected $pk = 'iconId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}