<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Headers:x-requested-with, content-type, token');

define('APP_PATH', __DIR__ . '/../application/');
define('WEB_HTTP', 'http://' . $_SERVER['HTTP_HOST'] . '/api');
define('WEB_HTTPS', 'https://' . $_SERVER['HTTP_HOST'] . '/api');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
