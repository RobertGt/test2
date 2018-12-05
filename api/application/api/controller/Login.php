<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/29 23:46
 * Email: 1183@mapgoo.net
 */

namespace app\api\controller;


use app\api\server\UserServer;
use app\api\validate\UserValidate;
use think\Request;

class Login extends Base
{
    public function realName(Request $request)
    {
        $param = [
            'front'      => $request->param('front',''),
            'contrary'   => $request->param('contrary',''),
            'hand'       => $request->param('hand',''),
            'realname'   => $this->userInfo['realname'],
            'uid'        => $this->userInfo['uid'],
        ];
        $validate = new UserValidate();
        if(!$validate->scene('real')->check($param)){
            ajax_info(1 , $validate->getError());
        }
        $response = (new UserServer())->real($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'提交失败');
        }
    }

    public function appUpload(Request $request)
    {
        $fileConfig = [
            'app'  =>  [
                'size'   =>  104857600,
                'ext'    =>  'apk,ipa'
            ]
        ];
        $files = $request->file();
        $root = ROOT_PATH . 'public';

        $file = '';
        foreach ($files as $key => $value){
            if(isset($fileConfig[$key])){
                $path = DS . 'uploads' . DS . $key;
                $validate = $fileConfig[$key];
                $file = $value;
                break;
            }
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate($validate)->move($root . $path);
            if($info){
                 $extension = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
                $appPath = str_replace('\\', '/', $path . DS . $info->getSaveName());
                if($extension == 'apk'){
                    $app = apkParseInfo($root . $appPath);
                    $app['path'] = $appPath;
                    print_r ($app);exit;
                }else{
					$zipFiles = (new \Chumper\Zipper\Zipper)->make($root . $appPath)->listFiles('/Info\.plist$/i');
					
					//$zipFiles = Zipper::make($this->targetFile)->listFiles('/Info\.plist$/i');
        
					$matched = 0;
					if ($zipFiles) {
						
						foreach ($zipFiles as $k => $filePath) {
							
							// 正则匹配包根目录中的Info.plist文件
							if (preg_match("/Payload\/([^\/]*)\/Info\.plist$/i", $filePath, $matches)) {
								$matched = 1;

								$this->app_folder = $matches[1];
						

								// 将plist文件解压到ipa目录中的对应包名目录中
								(new \Chumper\Zipper\Zipper)->make($root . $appPath)->folder('Payload/'.$this->app_folder)->extractMatchingRegex('/usr/local/www/test2/api/public/uploads/tmp/'.$this->app_folder, "/Info\.plist$/i");

								// 拼接plist文件完整路径
								$fp = '/usr/local/www/test2/api/public/uploads/tmp/'.$this->app_folder . '/Info.plist';
								
								// 获取plist文件内容
								$content = file_get_contents($fp);

								// 解析plist成数组
								$ipa = new \CFPropertyList\CFPropertyList();
								$ipa->parse($content);
								$ipaInfo = $ipa->toArray();
								$icon = !empty($ipaInfo['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles']) ? $ipaInfo['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles'] : [];
								if(count($icon) > 0){
									$iconName = array_pop($icon);
									$f = (new \Chumper\Zipper\Zipper)->make($root . $appPath)->listFiles("/{$iconName}/i");
									if(!empty($f)){
										$icon = array_pop($f);
									}
									$appPath = $root . $appPath;
									exec("unzip {$appPath} {$icon} -d " . '/usr/local/www/test2/api/public/uploads/tmp/');
								}
								// ipa 解包信息
								$this->ipa_data_bak = json_encode($ipaInfo);

								// 包名
								$this->package_name = $ipaInfo['CFBundleIdentifier'];
								
								// 版本名
								$this->version_name = $ipaInfo['CFBundleShortVersionString'];

								// 版本号
								$this->version_code = str_replace('.', '', $ipaInfo['CFBundleShortVersionString']);

								// 别名
								$this->bundle_name = $ipaInfo['CFBundleName'];

								// 显示名称
								$this->display_name =  $ipaInfo['CFBundleDisplayName'];
							}
						}

					}
                    print_r ($plist->toArray());exit;
                }
            }else{
                ajax_info(1,$file->getError());
            }
        }
        ajax_info(1,"文件上传失败");
    }
}