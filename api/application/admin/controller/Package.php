<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/24 12:05
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\PackageServer;
use app\admin\validate\PackageValidate;
use think\Request;

class Package extends Base
{
    public function packageInfo(Request $request)
    {
        $param = [
            'id'  => $request->param('id',0, 'intval'),
        ];

        $validate = new PackageValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new PackageServer())->packageInfo($param['id']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function packageList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval')
        ];

        $response = (new PackageServer())->packageList($param);

        ajax_info(0,'success', $response);
    }

    public function packageDelete(Request $request)
    {
        $param = [
            'id'  => $request->param('id',0, 'intval')
        ];

        $validate = new PackageValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new PackageServer())->packageDelete($param['id']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function packageInsert(Request $request)
    {
        $param = [
            'packageName' => $request->param('packageName',''),
            'packageType' => $request->param('packageType','') ? 1 : 0,
            'upload'      => $request->param('upload', 0, 'intval'),
            'download'    => $request->param('download', 0, 'intval'),
            'price'       => $request->param('price', '')
        ];

        $validate = new PackageValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new PackageServer())->packageInsert($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function packageUpdate(Request $request)
    {
        $param = [
            'id'  => $request->param('id',0, 'intval'),
            'packageName' => $request->param('packageName',''),
            'packageType' => $request->param('packageType','') ? 1 : 0,
            'upload'      => $request->param('upload', 0, 'intval'),
            'download'    => $request->param('download', 0, 'intval'),
            'price'       => $request->param('price', '')
        ];

        $validate = new PackageValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new PackageServer())->packageUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }

    public function payInfo(Request $request)
    {
        $response = (new PackageServer())->payInfo();
        ajax_info(0,'success', $response);
    }
    public function payUpdate(Request $request)
    {
        $param = [
            'wechat'      => $request->param('wechat', ''),
            'secret'      => $request->param('secret',''),
            'mchid'       => $request->param('mchid',''),
            'apikey'      => $request->param('apikey', ''),
            'alipay'      => $request->param('alipay', ''),
            'privateKey'  => $request->param('privateKey', ''),
            'publicKey'   => $request->param('publicKey', '')
        ];
        $response = (new PackageServer())->payUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}