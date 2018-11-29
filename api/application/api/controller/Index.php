<?php
namespace app\api\controller;

use think\Request;

class Index
{
    public function index()
    {
        return 'v1';
    }

    public function imageUpload(Request $request)
    {
        $fileConfig = [
            'image'  =>  [
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
