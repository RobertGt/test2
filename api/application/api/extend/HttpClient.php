<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/1/6 9:46
 * Email: 1183@mapgoo.net
 */

namespace app\api\extend;

use think\Log;

class HttpClient
{
    protected $connTimeOut = 5;
    protected $readTimeOut = 5;
    protected $header = [];
    public $status = 200;
    public function Request($Url , $Method = 'GET' , $PostData = [] , $ssl = false , $p = ''){
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$Url);
        if($this->header){
            curl_setopt($curl,CURLOPT_HTTPHEADER , $this->header);
        }
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        if($ssl){
            curl_setopt($curl,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($curl,CURLOPT_SSLCERT , $p .'/cert/apiclient_cert.pem');
            curl_setopt($curl,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($curl,CURLOPT_SSLKEY,$p . '/cert/apiclient_key.pem');
        }
        curl_setopt($curl,CURLOPT_TIMEOUT,$this->readTimeOut);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$this->connTimeOut);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        if($Method == 'POST')
        {
            curl_setopt($curl,CURLOPT_POST,true);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$PostData);
        }
        $response = curl_exec($curl);
        $this->status = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
        if($response === false) {
            Log::error('Curl Error : '.curl_error($curl) . ' Url : ' . $Url);
        }
        curl_close($curl);
        Log::info("请求URL: " . $Url . " ," . $Method . " Data:" . print_r ($PostData, true) . ", 返回的Json:". $response);
        return $response;
    }
    public function ConcurrentRequest($PushData = [] , $Method = 'GET'){
        $responseResult = [];
        if (empty($PushData))return $responseResult;
        $queue = curl_multi_init();
        $map = array();
        foreach ($PushData as $k => $push) {
            $ch = curl_init();
            if($Method == "GET"){
                curl_setopt($ch, CURLOPT_URL, $push['PushUrl'] . '?' . http_build_query($push['Body']));
            }else{
                curl_setopt($ch, CURLOPT_URL, $push['PushUrl']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($push['Body']));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            // 设置User-Agent
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mapgoo TravelEvent');
            // 连接建立最长耗时
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$this->connTimeOut);
            // 请求最长耗时
            curl_setopt($ch,CURLOPT_TIMEOUT,$this->readTimeOut);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // 设置Post参数
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $Method);
            // 设置headers
            if($this->header){
                curl_setopt($ch,CURLOPT_HTTPHEADER , $this->header);
            }
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_multi_add_handle($queue, $ch);
            $map[strval($ch)] = $k;
        }
        $responseResult = array();
        $callback = function($data){
            preg_match_all('/\{.*\}/i', $data, $matches);
            return isset($matches[0][0]) ? $matches[0][0] : '';
        };
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($code != CURLM_OK) {
                break;
            }
            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($queue)) {
                // get the content returned on the request
                $error = curl_error($done['handle']);
                $result = $callback(curl_multi_getcontent($done["handle"]));
                $url = $map[strval($done["handle"])];
                $check = json_decode($result, true);  //判断是否成功
                if(!empty($check)){
                    $responseResult[$url] = $check;
                }else{
                    $responseResult[$url] = false;
                }
                Log::info("批量请求URL: " . $PushData[$url]['PushUrl'] . " ,". $Method ." Data:".json_encode($PushData[$url]['Body']).", CurlError:".$error.", 返回的Json:". $result);
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }
        } while ($active);
        curl_multi_close($queue);
        return $responseResult;
    }
    
    public function setHeader($key , $value){
        $this->header[] = $key . ":" . $value;
    }

    public function setConnTimeOut($s)
    {
        $this->connTimeOut = $s;
    }

    public function setReadTimeOut($s)
    {
        $this->readTimeOut = $s;
    }
}