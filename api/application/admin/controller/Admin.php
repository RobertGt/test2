<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/21 0:57
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\AdminServer;
use app\admin\validate\LoginValidate;
use think\Request;

class Admin extends Base
{
    public function adminList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new AdminServer())->adminList($param);

        ajax_info(0,'success', $response);
    }

    public function adminDelete(Request $request)
    {
        $param = [
            'aid'  => $request->param('id',0, 'intval')
        ];

        if($param['aid'] == 1){
            ajax_info(1 , "无法删除系统管理员");
        }

        if($this->adminInfo['aid'] != 1){
            ajax_info(1 , "无权操作");
        }

        $validate = new LoginValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminDelete($param['aid']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function adminInsert(Request $request)
    {
        $param = [
            'account'  => $request->param('username',''),
            'password' => $request->param('newPassword',''),
            'remark'   => $request->param('remark','')
        ];

        if($this->adminInfo['aid'] != 1){
            ajax_info(1 , "无权操作");
        }

        $validate = new LoginValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminInsert($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function adminInfo(Request $request)
    {
        $param = [
            'aid'       => $request->param('aid',0, 'intval'),
        ];

        $validate = new LoginValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminInfo($param['aid']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function adminUpdate(Request $request)
    {
        $param = [
            'aid'       => $request->param('id',0, 'intval'),
            'account'  => $request->param('username',''),
            'password' => $request->param('editPassword',''),
            'remark'   => $request->param('remark','')
        ];

        if($this->adminInfo['aid'] != 1){
            ajax_info(1 , "无权操作");
        }

        $validate = new LoginValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}