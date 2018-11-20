<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/15 11:27
 * Email: 1183@mapgoo.net
 */

namespace app\api\validate;


use think\Validate;

class ArticleValidate extends Validate
{
    protected $rule = [
        'articleId'    => 'checkArticle'
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'checkArticle'  =>  ['articleId']
    ];

    public function checkArticle($articleId, $rule, $data)
    {
        if(!$articleId || !authcode($articleId)){
            return '文章错误';
        }

        return true;
    }
}