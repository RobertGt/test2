<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 23:32
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Exception;
use think\Log;
use think\Model;

class UserModel extends Model
{
    protected $table = 'bas_user';
    protected $pk = 'uid';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';

    public function register($param = [], $imei)
    {
        $where['openId'] = $param['openId'];
        $where['platform'] = $param['platform'];
        $user = $this->where($where)->field('uid')->find();
        $userDevicesModel = new UserDevicesModel();
        if($user){
            $uid = $user->getData('uid');
            $bind['uid'] = $uid;
            $bind['imei'] = $imei;
            $info = $userDevicesModel->where($bind)->find();
            if(!$info){
                try{
                    $userDevicesModel->create($bind);
                }catch (Exception $e){
                    Log::error("user bind error:" . $e->getMessage());
                    return false;
                }
            }
            return $uid;
        }
        try{
            $this->startTrans();
            $user = $this->create($param);
            $bind['uid'] = $user->uid;
            $bind['imei'] = $imei;
            $userDevicesModel->create($bind);
            $this->commit();
        }catch (Exception $e){
            $this->rollback();
            Log::error("register error:" . $e->getMessage());
            return false;
        }
        return $user->uid;
    }
}