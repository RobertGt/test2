<?php
namespace app\admin\controller;

use app\admin\server\AdminServer;
use app\admin\validate\LoginValidate;
use think\Request;

class Index
{
    public function login(Request $request)
    {
        $param = [
            'account'     => $request->param('username',''),
            'password'    => $request->param('password','')
        ];

        $validate = new LoginValidate();
        if(!$validate->scene('login')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->login($param);

        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'账号不存在或者密码错误');
        }
    }

    public function reset(Request $request)
    {
        $param = [
            'account'        => $request->param('username',''),
            'password'       => $request->param('password',''),
            'newPassword'    => $request->param('newPassword',''),
            'repeatPassword' => $request->param('repeatPassword','')
        ];

        $validate = new LoginValidate();
        if(!$validate->scene('reset')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->reset($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'重置密码失败');
        }
    }

    public function uploadFile(Request $request)
    {
        $fileConfig = [
            'icon'  =>  [
                'size'   =>  1048576,
                'ext'    =>  'jpg,png'
            ],
            'ring'  =>  [
                'size'   =>  5242880,
                'ext'    =>  'mp3,wma,mp4,act'
            ],
            'thumbFile'  =>  [
                'size'   =>  2097152,
                'ext'    =>  'jpg,png'
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
                $response['savePath'] = $path . DS . $info->getSaveName();
                $response['viewUrl'] = urlCompletion($response['savePath']);
                ajax_info(0, 'success', $response);
            }else{
                ajax_info(1,$file->getError());
            }
        }
        ajax_info(1,"文件上传失败");
    }

}
