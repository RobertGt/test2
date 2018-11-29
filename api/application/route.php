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

    'v1/register'           => 'api/User/register',
    'v1/sendMail'           => 'api/User/sendMail',
    'v1/login'              => 'api/User/login',
    'v1/find'               => 'api/User/passwordFind',
    'v1/imageUpload'        => 'api/Index/imageUpload',
    'v1/realName'           => 'api/Login/realName'
]);

Route::get([

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

    'admin/statistics'     => 'admin/Statistics/index',
    'admin/chart'          => 'admin/Statistics/chart',

    'admin/buyTop'         => 'admin/Statistics/buyTop',
    'admin/downTop'        => 'admin/Statistics/downTop',
    'admin/eventTop'       => 'admin/Statistics/eventTop',
]);
