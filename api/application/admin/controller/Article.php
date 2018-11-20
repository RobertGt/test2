<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 19:11
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\ArticleServer;
use app\admin\validate\ArticleValidate;
use think\Request;

class Article extends Base
{
    public function articleList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval')
        ];

        $response = (new ArticleServer())->articleList($param, WEB_HTTPS);

        ajax_info(0,'success', $response);
    }

    public function articleDelete(Request $request)
    {
        $param = [
            'articleId'  => $request->param('id',0, 'intval'),
        ];

        $validate = new ArticleValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ArticleServer())->articleDelete($param['articleId']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function articleInfo(Request $request)
    {
        $param = [
            'articleId'   => $request->param('articleId',0, 'intval'),
        ];

        $validate = new ArticleValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ArticleServer())->articleInfo($param['articleId']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function articleInsert(Request $request)
    {
        $param = [
            'aid'      => $this->adminInfo['aid'],
            'thumb'    => $request->param('thumb',''),
            'author'   => $request->param('author',''),
            'title'    => $request->param('title',''),
            'describe' => $request->param('describe',''),
            'content'  => $request->param('content',''),
            'state'    => $request->param('state') ? 1 : 0,
        ];

        $validate = new ArticleValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ArticleServer())->articleInsert($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function articleUpdate(Request $request)
    {
        $param = [
            'aid'       => $this->adminInfo['aid'],
            'thumb'     => $request->param('thumb',''),
            'author'    => $request->param('author',''),
            'title'     => $request->param('title',''),
            'describe'  => $request->param('describe',''),
            'content'   => $request->param('content',''),
            'state'     => $request->param('state') ? 1 : 0,
            'articleId' => $request->param('articleId',0, 'intval'),
        ];

        $validate = new ArticleValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ArticleServer())->articleUpdate($param);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }

    public function articlePublish(Request $request)
    {
        $param = [
            'state'     => $request->param('state') ? 1 : 0,
            'articleId' => $request->param('articleId',0, 'intval'),
        ];
        $validate = new ArticleValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new ArticleServer())->articlePublish($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function articleSort(Request $request)
    {
        $param = $request->param('order/a', []);

        $response = (new ArticleServer())->articleSort($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }
}