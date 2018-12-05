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
        'appName'     => 'require',
        'version'     => 'require',
        'package'     => 'require',
        'state'       => 'checkState',
        'upload'      => 'checkUpload',
    ];

    protected $message  =   [
        "appName.require"   =>  '上传包无法识别,请重新打包',
        "version.require"   =>  '上传包无法识别,请重新打包',
        "package.require"   =>  '上传包无法识别,请重新打包',
    ];

    protected $scene = [
        'upload'         =>  ['appName', 'version', 'package'],
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
}