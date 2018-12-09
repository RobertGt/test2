<?php
namespace app\api\controller;

use app\api\server\ApplicationServer;
use app\api\validate\ApplicationValidate;
use think\Request;

class Index
{
    public function index()
    {
        qrCode('weixin://wxpay/bizpayurl?pr=7SFe5uc');
        return 'v1';
    }

    public function appDownInfo(Request $request)
    {
        $param = [
            'appId'  => $request->param('appId','')
        ];
        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new ApplicationServer())->appDownInfo($param['appId']);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function appDownUrl(Request $request)
    {
        $param = [
            'appId'    => authcode($request->param('appId','')),
            'lat'      => $request->param('lat',''),
            'lng'      => $request->param('lng',''),
            'platform' => $request->param('platform',''),
        ];
        $validate = new ApplicationValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $appModel = new ApplicationServer();
        $response = $appModel->appDownUrl($param);
        if($response){
            ajax_info(0,'success', $response);
        }else{
            ajax_info(1, $appModel->errMsg);
        }
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
