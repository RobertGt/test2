<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace' => 'app',

    'log'           => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'          => 'File',
        // 日志保存目录
        'path'          => LOG_PATH,
        // 日志记录级别
        'level'         => [],
        //单个日志文件的大小限制，超过后会自动记录到第二个文件
        'file_size'     => 104857600,
        //独立写错误日志
        'apart_level'   => ['error']
    ],


    // 应用调试模式
    'app_debug' => false,
    //接管所有异常请求
    'exception_handle' => '\\app\\common\\exception\\Http',

    //全局过滤
    'default_filter' => 'trim',

    'url_html_suffix' => '',

    // 默认模块名
    'default_module'         => 'api',

    //app请求密钥
    'app_key'   =>  '7c6fe563abc91f43be71d5bc9072329f',

    /* 邮件 */
    'email' => [
        'SMTP_HOST'   => 'smtp.qq.com', //SMTP服务器
        'SMTP_PORT'   => '25', //SMTP服务器端口
        'SMTP_USER'   => '603003294@qq.com', //SMTP服务器用户名
        'SMTP_PASS'   => 'ettjdcgmhtafbbbf', //SMTP服务器密码
        'FROM_EMAIL'  => '603003294@qq.com', //发件人EMAIL
        'FROM_NAME'   => 'XXX官方', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
    ],
];
