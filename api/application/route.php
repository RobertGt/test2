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

use think\Route;

Route::post([
    'v1/taskCreate'        => 'api/Task/taskCreate',
    'v1/taskDelete'        => 'api/Task/taskDelete',
    'v1/taskUpdate'        => 'api/Task/taskUpdate',
    'v1/taskSort'          => 'api/Task/taskSort',
    'v1/taskUpload'        => 'api/Task/taskUpload',

    'v1/articleShare'      => 'api/article/articleShare',

    'v1/setting'           => 'api/Configure/setting',

    'admin/login'          => 'admin/Index/login',
    'admin/reset'          => 'admin/Index/reset',

    'admin/adminInsert'      => 'admin/Admin/adminInsert',
    'admin/adminUpdate'      => 'admin/Admin/adminUpdate',

    'admin/themeInsert'      => 'admin/Theme/themeInsert',
    'admin/themeUpdate'      => 'admin/Theme/themeUpdate',

    'admin/iconInsert'      => 'admin/Icon/iconInsert',
    'admin/iconUpdate'      => 'admin/Icon/iconUpdate',

    'admin/ringInsert'      => 'admin/Ring/ringInsert',
    'admin/ringUpdate'      => 'admin/Ring/ringUpdate',

    'admin/noiseInsert'      => 'admin/Noise/noiseInsert',
    'admin/noiseUpdate'      => 'admin/Noise/noiseUpdate',

    'admin/articleInsert'      => 'admin/Article/articleInsert',
    'admin/articleUpdate'      => 'admin/Article/articleUpdate',
    'admin/articlePublish'     => 'admin/Article/articlePublish',
    'admin/articleSort'        => 'admin/Article/articleSort',

    'admin/uploadFile'         => 'admin/Index/uploadFile',

    'v1/login'                 => 'api/User/login',
]);

Route::get([
    'v1/taskList'          => 'api/Task/taskList',
    'v1/taskIcon'          => 'api/Task/taskIcon',
    'v1/taskNext'          => 'api/Task/taskNext',

    'v1/taskStatistic'     => 'api/Statistic/taskStatistic',
    'v1/taskDistribution'  => 'api/Statistic/taskDistribution',
    'v1/taskCurve'         => 'api/Statistic/taskCurve',
    'v1/coveStatistic'     => 'api/Statistic/coveStatistic',
    'v1/coveCurve'         => 'api/Statistic/coveCurve',

    'v1/articleList'       => 'api/article/articleList',
    'v1/articleInfo'       => 'api/article/articleInfo',

    'v1/settingInfo'       => 'api/Configure/settingInfo',
    'v1/theme'             => 'api/Configure/theme',
    'v1/ring'              => 'api/Configure/ring',

    'admin/adminList'      => 'admin/Admin/adminList',
    'admin/adminDelete'    => 'admin/Admin/adminDelete',
    'admin/adminInfo'      => 'admin/Admin/adminInfo',

    'admin/userList'       => 'admin/User/userList',
    'admin/userDelete'     => 'admin/User/userDelete',


    'admin/themeList'      => 'admin/Theme/themeList',
    'admin/themeDelete'    => 'admin/Theme/themeDelete',
    'admin/themeInfo'      => 'admin/Theme/themeInfo',

    'admin/iconList'      => 'admin/Icon/iconList',
    'admin/iconDelete'    => 'admin/Icon/iconDelete',
    'admin/iconInfo'      => 'admin/Icon/iconInfo',

    'admin/ringList'      => 'admin/Ring/ringList',
    'admin/ringDelete'    => 'admin/Ring/ringDelete',
    'admin/ringInfo'      => 'admin/Ring/ringInfo',

    'admin/noiseList'      => 'admin/Noise/noiseList',
    'admin/noiseDelete'    => 'admin/Noise/noiseDelete',
    'admin/noiseInfo'      => 'admin/Noise/noiseInfo',

    'admin/articleList'      => 'admin/Article/articleList',
    'admin/articleDelete'    => 'admin/Article/articleDelete',
    'admin/articleInfo'      => 'admin/Article/articleInfo',
]);
