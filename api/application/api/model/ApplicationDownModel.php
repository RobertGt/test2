<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/6 1:14
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class ApplicationDownModel extends Model
{
    protected $table = 'bas_application_down';
    protected $pk = 'downId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = '';
}