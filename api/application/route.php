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

    'admin/userStateUpdate'     => 'admin/User/userStateUpdate',
    'admin/userRealnameUpdate'  => 'admin/User/userRealnameUpdate',
    'admin/userPasswordUpdate'  => 'admin/User/userPasswordUpdate',
    'admin/appStateUpdate'      => 'admin/Application/appStateUpdate',


    'admin/packageInsert'  => 'admin/Package/packageInsert',
    'admin/packageUpdate'  => 'admin/Package/packageUpdate',
    'admin/payUpdate'      => 'admin/Package/payUpdate',

    'admin/templetUpdateState'    => 'admin/Templet/templetUpdateState',
    'admin/templetUpdate'    => 'admin/Templet/templetUpdate',

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


    'admin/packageInfo'    => 'admin/Package/packageInfo',
    'admin/userInfo'       => 'admin/User/userInfo',
    'admin/appListByUser'  => 'admin/Application/appListByUser',
    'admin/appList'        => 'admin/Application/appList',
    'admin/appInfo'        => 'admin/Application/appInfo',
    'admin/updateList'     => 'admin/Application/updateList',
    'admin/downList'       => 'admin/Application/downList',

    'admin/packageList'    => 'admin/Package/packageList',
    'admin/packageDelete'  => 'admin/Package/packageDelete',
    'admin/payInfo'        => 'admin/Package/payInfo',

    'admin/templetList'    => 'admin/Templet/templetList',
    'admin/templetInfo'    => 'admin/Templet/templetInfo',
]);
