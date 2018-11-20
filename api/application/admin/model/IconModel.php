<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 18:16
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class IconModel extends Model
{
    protected $table = 'bas_icon';
    protected $pk = 'iconId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}