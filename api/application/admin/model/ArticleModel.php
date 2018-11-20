<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 19:15
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class ArticleModel extends Model
{
    protected $table = 'bas_article';
    protected $pk = 'articleId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
    protected $adminModel = 'sys_admin';

    public function articleList($where = [], $pageNum = 0, $pageSize = 10)
    {
        $article = $this->alias('a')
            ->join($this->adminModel . ' admin' , 'a.aid = admin.aid' , 'LEFT')
            ->field('admin.account, a.articleId id, a.title, a.thumb, a.describe, a.read, a.state, a.share, a.sort, a.createTime, a.author')
            ->where($where)
            ->page($pageNum, $pageSize)
            ->order('a.sort DESC, a.createTime DESC')
            ->select();
        return $article;
    }
}