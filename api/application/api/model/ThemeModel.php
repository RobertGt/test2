<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/15 16:42
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class ThemeModel extends Model
{
    protected $table = 'bas_theme';
    protected $pk = 'themeId';

    public function themeList()
    {
        $where['isDelete'] = 0;
        return $this->where($where)->field("themeId, taskColour, restColour")->order("sort DESC")->select();
    }
}