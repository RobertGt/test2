<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 输出json
 * @param int $error 错误编码
 * @param string $reason 错误描述
 * @param null $result 数据
 * @param bool $array 是否是数组
 */
function ajax_info($error = 0, $reason = 'success', $result = '', $array = true)
{
    header('Content-type: application/json;charset=utf-8');
    if ($array) {
        $json = json_encode(['error' => $error, 'reason' => $reason, 'result' => $result], JSON_UNESCAPED_UNICODE);
    } else {
        $json = json_encode(['error' => $error, 'reason' => $reason, 'result' => $result], JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }
    exit($json);
}

/**
 * 获取随机字符串
 * @param int $length
 * @return string
 */
function getRandStr($length = 4)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $str;
}

/**
 * 获取客户端IP地址
 * @return string
 */
function getClientIp()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (!empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);

    return $cip;
}

/**
 * 加/解密字符串
 * @param $string  需要加密/解密字符串
 * @param string $operation  DECODE表示解密,ENCODE表示加密
 * @param string $key  加密密钥
 * @param int $expiry   有效时间
 * @return bool|string
 */
function authcode($string, $operation = 'DECODE', $key = 'b8e40baf97fba28e1bb66133dfa6d7f3', $expiry = 0) {
     if($operation == 'DECODE'){
        $string = base64_decode(str_replace(['-', '_'], ['+', '/'], $string));
    }
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙
    $key = md5($key);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
    //解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        //str_replace('=', '', base64_encode($result))
        $str = $keyc . base64_encode($result);
        return str_replace(['+', '/'], ['-', '_'], base64_encode($str));
    }
}

/**
 * 链接是否需要重新拼接前缀
 * @param string  $url  需要拼接的URL
 * @return string
 */
function urlCompletion($url)
{
    if(!$url)return '';
    if(!filter_var($url,FILTER_VALIDATE_URL)){
        $url = str_replace('\\', '/', $url);
        $url = WEB_HTTP . $url;
    }
    return $url;
}

function byteToMb($size)
{
    return round($size / 1024 / 1024, 2) . 'MB';
}

/**
 * 字符串截取，支持中文和其他编码
 * static
 * access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * return string
 */
function msubstrs($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}

/**
 * 邮箱验证
 *
 * @return boolean
 */
function _checkemail($email=''){

    if(strlen($email)<5){
        return false;
    }
    $res="/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/";
    if(preg_match($res,$email)){
        return true;
    }else{
        return false;
    }
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @return boolean
 */
function think_send_mail($to, $name, $body = '', $subject = '测试邮件'){
    $config = \think\Config::get('email');
    $mail             = new \PHPMailer\PHPMailer\PHPMailer(); //PHPMailer对象
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    if($config['SMTP_PORT'] != 25){
        $mail->SMTPSecure = 'ssl';		      // 使用安全协议
    }
    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);

    return $mail->Send() ? true : $mail->ErrorInfo;
}

function ipaParseInfo($apk) {
    $apkinfo = [];
    $zipper = new \Chumper\Zipper\Zipper();
    $zipFiles = $zipper->make($apk)->listFiles('/Info\.plist$/i');
    $root = ROOT_PATH . 'public';
    $path = '/uploads/tmp/' . basename($apk, '.ipa');
    $temp_save_path = $root . $path . "/";
    $matched = 0;
    if ($zipFiles) {
        foreach ($zipFiles as $k => $filePath) {
            // 正则匹配包根目录中的Info.plist文件
            if (preg_match("/Payload\/([^\/]*)\/Info\.plist$/i", $filePath, $matches)) {
                $matched = 1;
                $appFolder = $matches[1];
                // 将plist文件解压到ipa目录中的对应包名目录中
                (new \Chumper\Zipper\Zipper())->make($apk)
                    ->folder('Payload/' . $appFolder)
                    ->extractMatchingRegex($temp_save_path , "/Info\.plist$/i");
                // 拼接plist文件完整路径
                $fp = $temp_save_path . 'Info.plist';
                // 获取plist文件内容
                $content = file_get_contents($fp);
                // 解析plist成数组
                $ipa = new \CFPropertyList\CFPropertyList();
                $ipa->parse($content);
                $ipaInfo = $ipa->toArray();
                $apkinfo['icon'] = '/uploads/tmp/icon.png';
                $icon = !empty($ipaInfo['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles']) ? $ipaInfo['CFBundleIcons']['CFBundlePrimaryIcon']['CFBundleIconFiles'] : [];
                if(count($icon) > 0){
                    $iconName = array_pop($icon);
                    $f = (new \Chumper\Zipper\Zipper)->make($apk)->listFiles("/{$iconName}/i");
                    if(!empty($f)){
                        $icon = array_pop($f);
                    }
                    exec("unzip {$apk} {$icon} -d " . $temp_save_path);

                    $parser = new \app\api\server\ParserServer();
                    $ipaFilePath = $temp_save_path . "{$icon}";
                    $pngImgName = $temp_save_path . 'icon.png';
                    $apkinfo['icon'] = $path . '/' . 'icon.png';
                    $parser::fix($ipaFilePath, $pngImgName);
                }
                // 包名
                $apkinfo['package'] = $ipaInfo['CFBundleIdentifier'];
                // 版本名
                $apkinfo['version'] = $ipaInfo['CFBundleShortVersionString'];
                $apkinfo['code'] = str_replace('.', '', $ipaInfo['CFBundleShortVersionString']);
                // 别名
                $apkinfo['appName'] = $ipaInfo['CFBundleName'];//!empty($ipaInfo['CFBundleDisplayName']) ? $ipaInfo['CFBundleDisplayName'] : $ipaInfo['CFBundleName'];
            }
        }
    }
    if(!$matched)return false;
    return $apkinfo;
}

function apkParseInfo($apk) {

    $aapt = 'aapt';// 这里其实是aapt的路径，不过我已经ln到/usr/local/aapt了。就不用了。
    $root = ROOT_PATH . 'public';
    $temp_save_path = $root . '/uploads/tmp/';

    exec("{$aapt} d badging {$apk}", $output, $return);

    // 解析错误
    if ( $return !== 0 ) {
        return false;
    }

    $output = implode(PHP_EOL, $output);
    //$apkinfo = new \stdClass;

    // 对外显示名称
    $pattern = "/application: label='(.*)'/isU";
    $results = preg_match($pattern, $output, $res);
    $apkinfo['appName'] = $results ? $res[1] : '';

    // 内部名称，软件唯一的
    $pattern = "/package: name='(.*)'/isU";
    $results = preg_match($pattern, $output, $res);
    $apkinfo['package'] = $results ? $res[1] : '';

    // 内部版本名称，用于检查升级
    $pattern = "/versionCode='(.*)'/isU";
    $results = preg_match($pattern, $output, $res);
    $apkinfo['code'] = $results ? $res[1] : 0;

    // 对外显示的版本名称
    $pattern = "/versionName='(.*)'/isU";
    $results = preg_match($pattern, $output, $res);
    $apkinfo['version'] = $results ? $res[1] : '';
    /*
    // 系统支持
    $pattern = "/sdkVersion:'(.*)'/isU";
    $results = preg_match($pattern, $output, $res);
    $apkinfo->sdk_version = $results ? $res[1] : 0;

    // 分辨率支持
    $densities = array(
        "/densities: '(.*)'/isU",
        "/densities: '120' '(.*)'/isU",
        "/densities: '160' '(.*)'/isU",
        "/densities: '240' '(.*)'/isU",
        "/densities: '120' '160' '(.*)'/isU",
        "/densities: '160' '240' '(.*)'/isU",
        "/densities: '120' '160' '240' '(.*)'/isU"
    );

    foreach($densities AS $k=>$v) {
        if( preg_match($v, $output, $res) ) {
            $apkinfo->densities[] = $res[1];
        }
    }

    // 应用权限
    $pattern = "/uses-permission: name='(.*)'/isU";
    $results = preg_match_all($pattern, $output, $res);
    $apkinfo->permissions = $results ? $res[1] : '';

    // 需要的功能（硬件支持）
    $pattern = "/uses-feature: name='(.*)'/isU";
    $results = preg_match_all($pattern, $output, $res);
    $apkinfo->features = $results ? $res[1] : '';
    */
    $apkinfo['icon'] = '/uploads/tmp/icon.png';
    // 应用图标路径
    if( preg_match("/icon='(.+)'/isU", $output, $res) ) {

        $icon_draw = trim( $res[1] );
        $icon_hdpi = 'res/drawable-hdpi/' . basename($icon_draw);

        $temp =$temp_save_path.basename($apk, '.apk') . DIRECTORY_SEPARATOR;

        if( @is_dir($temp) === FALSE ) {
            mkdir($temp,0777,true);
        }

        exec("unzip {$apk} {$icon_draw} -d " . $temp);
        exec("unzip {$apk} {$icon_hdpi} -d " . $temp);

        $icon_draw_abs = $temp . $icon_draw;
        $icon_hdpi_abs = $temp . $icon_hdpi;

        $apkinfo['icon'] = @is_file($icon_hdpi_abs) ? str_replace($root, '', $icon_hdpi_abs) :
            (@is_file($icon_draw_abs) ? str_replace($root, '', $icon_draw_abs) : '/uploads/tmp/icon.png');
    }

    return $apkinfo;
}

function qrCode($url, $logo = '', $platform = '')
{
    import('kairos.phpqrcode.qrlib');
    $root = ROOT_PATH . 'public';
    $path = DS . 'uploads/code/';
    if(!file_exists($root . $path)){
        mkdir($root . $path, 0700,true);
    }
    $fileName = md5($url) . '.png';
    $filePath = $root . $path . $fileName;

    if(!file_exists($filePath)){
        $object = new \Qrcode();
        $errorCorrectionLevel = 12;//容错级别
        $matrixPointSize = 13;//生成图片大小
        ob_end_clean();//清空缓冲区
        $object->png($url, $filePath, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    if ($logo && file_exists($root . $logo) && $platform != 'ios') {
        $QR = imagecreatefromstring(file_get_contents($filePath));  //目标图象连接资源。
        $logo = imagecreatefromstring(file_get_contents($root . $logo));    //源图象连接资源。
        if (imageistruecolor($logo)) imagetruecolortopalette($logo, false, 65535);//解决logo失真问题
        $QR_width = imagesx($QR);           //二维码图片宽度
        $QR_height = imagesy($QR);  //二维码图片高度
        $logo_width = imagesx($logo);       //logo图片宽度
        $logo_height = imagesy($logo);      //logo图片高度
        $logo_qr_width = $QR_width / 5;     //组合之后logo的宽度(占二维码的1/5)
        $scale = $logo_width/$logo_qr_width;    //logo的宽度缩放比(本身宽度/组合后的宽度)
        $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
        $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点

        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0,   $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
        imagepng($QR, $filePath);
        imagedestroy($QR);
        imagedestroy($logo);
    }

    return $path . $fileName;
}

//微信支付数组转xml
function arrayToXml($arr)
{
    header('Content-Type:text/xml; charset=utf-8');
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

        } else
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
    }
    $xml .= "</xml>";
    echo $xml;
    exit;
}

