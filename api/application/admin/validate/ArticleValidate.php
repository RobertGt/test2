<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/23 21:36
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class ArticleValidate extends Validate
{
    protected $rule = [
        'articleId'      => 'require',
        'thumb'          => 'require',
        'author'         => 'require',
        'title'          => 'checkTitle',
        'describe'       => 'checkDescribe',
        'content'        => 'require'
    ];

    protected $scene = [
        'insert'         =>  ['thumb', 'author', 'title', 'describe', 'content'],
        'update'         =>  ['articleId', 'thumb', 'author', 'title', 'describe', 'content'],
        'checkId'        =>  ['articleId'],
    ];

    public function checkTitle($title, $rule, $data)
    {
        if(!$title){
            return '标题不能为空';
        }

        if(mb_strlen($title, 'utf8') > 20){
            return '标题不得超过20位字符';
        }
        return true;
    }

    public function checkDescribe($describe, $rule, $data)
    {
        if(!$describe){
            return '摘要不能为空';
        }

        if(mb_strlen($describe, 'utf8') > 120){
            return '摘要不得超过120位字符';
        }
        return true;
    }
}