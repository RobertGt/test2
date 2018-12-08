<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/29 23:46
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\model\UserModel;
use app\api\server\ApplicationServer;
use app\api\server\MessageServer;
use app\api\server\UserServer;
use app\api\validate\ApplicationValidate;
use app\api\validate\UserValidate;
use think\Cache;
use think\Request;

class Login extends Base
{
    public function realName(Request $request)
    {
        $param = [
            'front'      => $request->param('front',''),
            'contrary'   => $request->param('contrary',''),
            'hand'       => $request->param('hand',''),
            'realname'   => $this->userInfo['realname'],
            'uid'        => $this->userInfo['uid'],
        ];
        $validate = new UserValidate();
        if(!$validate->scene('real')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->real($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'提交失败');
        }
    }

    public function userInfo(Request $request)
    {
        $response = (new UserServer())->userInfo($this->userInfo['uid']);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'获取失败');
        }
    }

    public function userUpdate(Request $request)
    {
        $param = [
            'password'   => $request->param('password',''),
            'oldPassword'=> $request->param('oldPassword',''),
            'wechat'     => $request->param('wechat',''),
            'imNumber'   => $request->param('imNumber', ''),
            'company'    => $request->param('company',''),
            'job'        => $request->param('job',''),
            'mobile'     => $request->param('mobile',''),
            'uid'        => $this->userInfo['uid']
        ];
        $validate = new UserValidate();
        if(!$validate->scene('edit')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        unset($param['oldPassword']);
        $response = (new UserServer())->userUpdate($param, $param['uid']);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }

    public function sendMailUpdate(Request $request){
        $response = (new UserServer())->sendMailUpdate($this->userInfo['uid']);
        if($response === true){
            ajax_info(0, 'success');
        }else{
            ajax_info(1, $response);
        }
    }

    public function checkCode(Request $request)
    {
        $param = [
            'code'      => $request->param('code',''),
            'find'      => 1
        ];
        $userInfo = (new UserModel())->where(['uid' => $this->userInfo['uid']])->field('email')->find()->toArray();
        $param['email'] = $userInfo['email'];
        $validate = new UserValidate();
        if(!$validate->scene('checkCode')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $key = md5($this->userInfo['uid'] . time());
        Cache::set($key, $this->userInfo['uid'], 60 * 10);
        ajax_info(0, 'success', ['updateKey' => $key]);
    }

    public function emailUpdate(Request $request)
    {
        $param = [
            'email'      => $request->param('email',''),
            'code'       => $request->param('code',''),
            'updateKey'  => $request->param('updateKey','')
        ];
        $validate = new UserValidate();
        if(!$validate->scene('emailUpdate')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->emailUpdate($param, $this->userInfo['uid']);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }

    public function messageList(Request $request)
    {
        $param = [
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval')
        ];
        $response = (new MessageServer())->messageList($param, $this->userInfo['uid']);

        ajax_info(0,'success', $response);
    }

    public function messageUpdate(Request $request)
    {
        $param = [
            'recId'  => $request->param('recId', '')
        ];
        $response = (new MessageServer())->messageUpdate($param['recId'], $this->userInfo['uid']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function messageFind(Request $request)
    {
        $response = (new MessageServer())->messageFind($this->userInfo['uid']);
        if($response){
            ajax_info(0, 'success', $response);
        }else{
            ajax_info(1, '无新消息');
        }
    }

    public function appUpload(Request $request)
    {
        $fileConfig = [
            'file'  =>  [
                'size'   =>  104857600,
                'ext'    =>  'apk,ipa'
            ]
        ];
        $files = $request->file();
        $root = ROOT_PATH . 'public';

        $file = '';
        foreach ($files as $key => $value){
            if(isset($fileConfig[$key])){
                $path = DS . 'uploads' . DS . $key;
                $validate = $fileConfig[$key];
                $file = $value;
                break;
            }
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate($validate)->move($root . $path);
            if($info){
                $extension = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
                $appPath = str_replace('\\', '/', $path . DS . $info->getSaveName());
                if($extension == 'apk'){
                    $param = apkParseInfo($root . $appPath);
                }else{
                    $param = ipaParseInfo($root . $appPath);
                }
                if(!$param){
                    ajax_info(1, "上传包无法识别,请重新打包");
                }
                $param['path'] = $appPath;
                $param['ext'] = $extension;
                $param['uid'] = $this->userInfo['uid'];
                $param['state'] = $this->userInfo['state'];
                $param['upload'] = $this->userInfo['upload'];
                $param['size'] = $file->getInfo('size');
                $validate = new ApplicationValidate();
                if(!$validate->scene('upload')->check($param)){
                    ajax_info(1 , $validate->getError());
                }
                $response = (new ApplicationServer())->upload($param);
                if($response){
                    ajax_info(0, 'Success');
                }else{
                    ajax_info(1, '操作失败');
                }
            }else{
                ajax_info(1,$file->getError());
            }
        }
        ajax_info(1,"文件上传失败");
    }
}