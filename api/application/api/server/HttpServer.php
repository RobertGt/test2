<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/30 23:16
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;

use think\Log;

class HttpServer
{
    protected $connTimeOut = 5;
    protected $readTimeOut = 5;
    protected $header = [];
    public $status = 200;

    public function request($url , $method = 'GET' , $postData = []){
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        if($this->header){
            curl_setopt($curl,CURLOPT_HTTPHEADER , $this->header);
        }
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);

        curl_setopt($curl,CURLOPT_TIMEOUT,$this->readTimeOut);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$this->connTimeOut);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        if($method == 'POST')
        {
            curl_setopt($curl,CURLOPT_POST,true);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$postData);
        }
        $response = curl_exec($curl);
        $this->status = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        if($response === false) {
            Log::error('Curl Error : '.curl_error($curl) . ' url : ' . $url);
        }
        curl_close($curl);
        return $response;
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