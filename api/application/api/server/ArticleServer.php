<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/15 11:31
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\ArticleModel;
use think\Exception;
use think\Log;

class ArticleServer
{
    public function articleList($param = [], $domain = '')
    {
        $article = (new ArticleModel())->articleList($param['pageNum'], $param['pageSize']);

        $articleList = [];

        foreach ($article as $value){
            $info = $value->getData();
            $info['articleId'] = authcode($info['articleId'], 'ENCODE');
            $info['thumb'] = urlCompletion($info['thumb']);
            $info['read'] = $info['read'] >= 10000 ? round($info['read'] / 10000, 1) . "万" : (string)$info['read'];
            $info['share'] = $info['share'] > 1000 ? $info['share'] . '+' : (string)$info['share'];
            $info['url'] = sprintf($domain . "/v1/articleInfo?articleId=%s", urlencode($info['articleId']));
            $articleList[] = $info;
        }

        return $articleList;
    }

    public function articleShare($articleId = '')
    {
        $articleId = authcode($articleId);

        try{
            $response = (new ArticleModel())->articleShare($articleId);
        }catch (Exception $e){
            Log::error("articleShare error:" . $e->getMessage());
            return false;
        }

        return $response;
    }

    public function articleInfo($articleId = '', $view = false)
    {
        $articleId = authcode($articleId);

        try{
            $response = (new ArticleModel())->articleInfo($articleId, $view);
        }catch (Exception $e){
            Log::error("articleInfo error:" . $e->getMessage());
            return false;
        }
        if($response){
            $response = $response->getData();
            $response['read'] = $response['read'] >= 10000 ? round($response['read'] / 10000, 1) . "万" : (string)$response['read'];
            $response['share'] = $response['share'] > 1000 ? $response['share'] . '+' : (string)$response['share'];
            $response['createTime'] = date('Y年m月d日', $response['createTime']);
            return $response;
        }else{
            return false;
        }
    }
}