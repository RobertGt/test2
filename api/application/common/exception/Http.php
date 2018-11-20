<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/12 23:21
 * Email: 1183@mapgoo.net
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\Log;

class Http extends Handle
{
    public function render(Exception $e){

        if ($e instanceof HttpException) {
            ajax_info($e->getStatusCode(), $e->getMessage());
        }
        //其他错误不做显示，直接打印到错误log
        Log::error('Request ErrorMsg : ' . $e->getMessage() . ' File : ' . $e->getFile() . ' Line : ' . $e->getLine());
        ajax_info(500, '未知错误!!!');
    }
}