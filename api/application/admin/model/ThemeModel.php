<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:30
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class ThemeModel extends Model
{
    protected $table = 'bas_theme';
    protected $pk = 'themeId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}