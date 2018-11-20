<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/13 0:05
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\ArticleServer;
use app\api\validate\ArticleValidate;
use think\Request;

class Article extends Base
{
    public function articleList(Request $request)
    {
        $param = [
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new ArticleServer())->articleList($param, WEB_HTTP);

        ajax_info(0,'success', $response);
    }

    public function articleShare(Request $request)
    {
        $param = [
            'articleId'  => $request->param('articleId','')
        ];

        $validate = new ArticleValidate();
        if(!$validate->scene('checkArticle')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new ArticleServer())->articleShare($param['articleId']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'分享上传失败');
        }
    }

    public function articleInfo(Request $request)
    {
        $param = [
            'articleId'  => $request->param('articleId',''),
            'view'       => $request->param('view') ? 1 : 0
        ];

        $validate = new ArticleValidate();
        if(!$validate->scene('checkArticle')->check($param)){
            exit();
        }

        $response = (new ArticleServer())->articleInfo($param['articleId'], $param['view']);

        if($response){
            return view('articleInfo', ['response' => $response]);
        }else{
            exit();
        }
    }
}