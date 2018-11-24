<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/25 1:22
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class DownloadModel extends Model
{
    protected $table = 'bas_application_down';
    protected $pk = 'downId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = '';
}