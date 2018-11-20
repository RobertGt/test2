<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/15 16:49
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class RingModel extends Model
{
    protected $table = 'bas_ring';
    protected $pk = 'ringId';

    public function ringList($noise = 0)
    {
        $where['noise'] = $noise;
        $where['isDelete'] = 0;
        return $this->where($where)->field("ringId, ringName, ringUrl")->order("sort DESC")->select();
    }

	public function ringGetFirst($noise = 0)
	{
		$where['noise'] = $noise;
        $where['isDelete'] = 0;
        $info = $this->where($where)->field("ringId, ringName, ringUrl")->order("sort DESC")->find();
		return $info->getData();
	}
}