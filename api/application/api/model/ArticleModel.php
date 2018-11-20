<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/15 11:35
 * Email: 1183@mapgoo.net
 */

namespace app\api\model;


use think\Model;

class ArticleModel extends Model
{
    protected $table = 'bas_article';
    protected $pk = 'articleId';

    public function articleList($pageNum = 1, $pageSize = 10)
    {
        $where['state'] = 1;
        return $this->field("articleId, thumb, title, describe, read, share")
                    ->where($where)
                    ->page($pageNum, $pageSize)
                    ->order("sort DESC, updateTime DESC")
                    ->select();
    }

    public function articleShare($articleId)
    {
        $where['state'] = 1;
        $where['articleId'] = $articleId;
        return $this->where($where)->setInc('share');
    }

    public function articleInfo($articleId, $view = false)
    {
        //$where['state'] = 1;
        $where['articleId'] = $articleId;
        if(!$view){
            $this->where($where)->setInc('read');
        }
        return $this->where($where)->field('title, content, read, share, author, createTime')->find();
    }
}