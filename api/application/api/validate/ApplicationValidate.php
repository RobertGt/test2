<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/12/5 23:12
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use app\api\model\ApplicationModel;
use think\Cache;
use think\Validate;

class ApplicationValidate extends Validate
{
    protected $rule = [
        'appId'       => 'require',
        'appName'     => 'require',
        'version'     => 'require',
        'package'     => 'require',
        'state'       => 'checkState',
        'upload'      => 'checkUpload',
        'appUrl'      => 'require|url',
        'appName'     => 'require',
        'sortUrl'     => 'require|checkSort',
    ];

    protected $message  =   [
        "appName.require"   =>  '上传包无法识别,请重新打包',
        "version.require"   =>  '上传包无法识别,请重新打包',
        "package.require"   =>  '上传包无法识别,请重新打包',
        'appUrl.require'    =>  '请输入商店地址',
        'appUrl.url'        =>  '请输入正确的商店地址',
        'appName.require'   =>  '请输入APP名称',
        'sortUrl.require'   =>  '请输入APP短链接'
    ];

    protected $scene = [
        'upload'         =>  ['appName', 'version', 'package'],
        'checkId'        =>  ['appId'],
        'appUrlUpdate'   =>  ['appId', 'appUrl'],
        'appUpdate'      =>  ['appId', 'appName', 'sortUrl'],
    ];

    public function checkState($state, $rule, $data)
    {
        $realName = Cache::get('realName', 1);
        if($realName && $state != 1){
            return "请先进行实名验证在上传应用";
        }
        return true;
    }

    public function checkUpload($upload, $rule, $data)
    {
        if(!$upload){
            return "上传应用已到达上限";
        }
        $where['uid'] = $data['uid'];
        $where['createTime'] = ['egt', strtotime(date('Y-m-d'))];
        $num = (new ApplicationModel())->where($where)->count();
        if($num >= $upload){
            return "已经超过今日上传限制";
        }
        return true;
    }

    public function checkSort($sortUrl, $rule, $data)
    {
        $isTure = preg_match('/^[0-9a-zA-Z]+$/',$sortUrl);
        if(!$isTure){
            return "短链接只能是字母与数字";
        }

        $where['appId'] = ['neq', $data['appId']];
        $where['sortUrl'] = $sortUrl;
        $num = (new ApplicationModel())->where($where)->count();
        if($num){
            return "短链接已被占用";
        }
        return true;
    }
}