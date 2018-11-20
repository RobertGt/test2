<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:54
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\IconServer;
use app\admin\validate\IconValidate;
use think\Request;

class Icon extends Base
{
    public function iconList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new IconServer())->iconList($param);

        ajax_info(0,'success', $response);
    }

    public function iconDelete(Request $request)
    {
        $param = [
            'iconId'  => $request->param('id',0, 'intval')
        ];

        $validate = new IconValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new IconServer())->iconDelete($param['iconId']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function iconInsert(Request $request)
    {
        $param = [
            'name'     => $request->param('name',''),
            'iconUrl'  => $request->param('iconUrl','')
        ];

        $validate = new IconValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new IconServer())->iconInsert($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function iconInfo(Request $request)
    {
        $param = [
            'iconId'       => $request->param('iconId',0, 'intval'),
        ];

        $validate = new IconValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new IconServer())->iconInfo($param['iconId']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function iconUpdate(Request $request)
    {
        $param = [
            'iconId'     => $request->param('iconId',0, 'intval'),
            'name'       => $request->param('name',''),
            'iconUrl'    => $request->param('iconUrl','')
        ];

        $validate = new IconValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new IconServer())->iconUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}