<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 19:13
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\ArticleModel;
use think\Exception;
use think\Log;

class ArticleServer
{
    public function articleList($param = [], $domain)
    {
        $where = [];
        if(!empty($param['seach']['title'])){
            $where['title'] = ['like', '%' . $param['seach']['title'] . '%'];
        }

        $articleModel = new ArticleModel();
        $total = $articleModel->where($where)->count();
        $list = $articleModel->articleList($where, $param['pageNum'], $param['pageSize']);
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['thumb'] = urlCompletion($info['thumb']);
            $info['describe'] = msubstrs($info['describe'],0, 20, 'utf-8');
            $info['url'] = sprintf($domain . "/v1/articleInfo?articleId=%s&view=%d", urlencode(authcode($info['id'], 'ENCODE')), 1);
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function articleDelete($articleId = 0)
    {
        try{
            $where['articleId'] = $articleId;
            (new ArticleModel())->where($where)->delete();
        }catch (Exception $e){
            Log::error("articleDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function articleInfo($articleId = 0)
    {
        $where['articleId'] = $articleId;

        $info = (new ArticleModel())->field('articleId, thumb, describe, content, createTime, author, title')->where($where)->find();

        if($info){
            $info = $info->getData();
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['viewUrl'] = urlCompletion($info['thumb']);
        }else{
            $info = [];
        }
        return $info;
    }

    public function articleInsert($param = [])
    {
        $create['aid']    = $param['aid'];
        $create['thumb']  = $param['thumb'];
        $create['author'] = $param['author'];
        $create['title'] = $param['title'];
        $create['describe'] = $param['describe'];
        $create['content'] = $param['content'];
        $create['state'] = $param['state'];
        try{
            (new ArticleModel())->create($create);
        }catch (Exception $e){
            Log::error("articleInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function articleUpdate($param = [])
    {
        $save['aid']    = $param['aid'];
        $save['thumb']  = $param['thumb'];
        $save['author'] = $param['author'];
        $save['title'] = $param['title'];
        $save['describe'] = $param['describe'];
        $save['content'] = $param['content'];
        $save['state'] = $param['state'];
        $where['articleId'] = $param['articleId'];
        try{
            (new ArticleModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("articleUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function articlePublish($param = [])
    {
        $save['state'] = $param['state'];
        $where['articleId'] = $param['articleId'];
        try{
            (new ArticleModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("articlePublish error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function articleSort($param = [])
    {
        $articleModel = new ArticleModel();
        try{
            foreach ($param as $key => $value){
                $where['articleId'] = $key;
                $save['sort'] = $value;
                $articleModel->save($save, $where);
            }
        }catch (Exception $e){
            Log::error("articleSort error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}