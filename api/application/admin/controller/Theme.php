<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 17:28
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\ThemeServer;
use app\admin\validate\ThemeValidate;
use think\Request;

class Theme extends Base
{
    public function themeList(Request $request)
    {
        $param = [
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new ThemeServer())->ThemeList($param);

        ajax_info(0,'success', $response);
    }

    public function themeDelete(Request $request)
    {
        $param = [
            'themeId'  => $request->param('id',0, 'intval')
        ];

        $validate = new ThemeValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ThemeServer())->themeDelete($param['themeId']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function themeInsert(Request $request)
    {
        $param = [
            'taskColour'  => $request->param('taskColour',''),
            'restColour'  => $request->param('restColour','')
        ];

        $validate = new ThemeValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ThemeServer())->themeInsert($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function themeInfo(Request $request)
    {
        $param = [
            'themeId'       => $request->param('themeId',0, 'intval'),
        ];

        $validate = new ThemeValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ThemeServer())->themeInfo($param['themeId']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function themeUpdate(Request $request)
    {
        $param = [
            'themeId'     => $request->param('themeId',0, 'intval'),
            'taskColour'  => $request->param('taskColour',''),
            'restColour'  => $request->param('restColour','')
        ];

        $validate = new ThemeValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ThemeServer())->themeUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}