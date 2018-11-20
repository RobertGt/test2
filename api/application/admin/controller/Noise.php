<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 18:31
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\RingServer;
use app\admin\validate\RingValidate;
use think\Request;

class Noise extends Base
{
    public function noiseList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
            'noise'    => 1,
        ];

        $response = (new RingServer())->ringList($param);

        ajax_info(0,'success', $response);
    }

    public function noiseDelete(Request $request)
    {
        $param = [
            'ringId'  => $request->param('id',0, 'intval'),
        ];

        $validate = new RingValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RingServer())->ringDelete($param['ringId']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function noiseInsert(Request $request)
    {
        $param = [
            'ringName' => $request->param('ringName',''),
            'ringUrl'  => $request->param('ringUrl',''),
            'noise'    => 1,
        ];

        $validate = new RingValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RingServer())->ringInsert($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function noiseInfo(Request $request)
    {
        $param = [
            'ringId'       => $request->param('ringId',0, 'intval'),
        ];

        $validate = new RingValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RingServer())->ringInfo($param['ringId']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function noiseUpdate(Request $request)
    {
        $param = [
            'ringId'     => $request->param('ringId',0, 'intval'),
            'ringName'   => $request->param('ringName',''),
            'ringUrl'    => $request->param('ringUrl','')
        ];

        $validate = new RingValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RingServer())->ringUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}