<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/29 23:46
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\UserServer;
use app\api\validate\UserValidate;
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

    public function appUpload(Request $request)
    {
        $fileConfig = [
            'app'  =>  [
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
                $response['savePath'] = str_replace('\\', '/', $path . DS . $info->getSaveName());
                $response['viewUrl'] = urlCompletion($response['savePath']);
                ajax_info(0, 'success', $response);
            }else{
                ajax_info(1,$file->getError());
            }
        }
        ajax_info(1,"文件上传失败");
    }
}