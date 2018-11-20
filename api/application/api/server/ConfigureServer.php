<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/15 16:41
 * Email: 1183@mapgoo.net
 */

namespace app\api\server;


use app\api\model\ConfigureModel;
use app\api\model\RingModel;
use app\api\model\ThemeModel;
use think\Exception;
use think\Log;

class ConfigureServer
{
    public function theme()
    {
        $theme = (new ThemeModel())->themeList();

        $themeList = [];

        foreach ($theme as $value){
            $info = $value->getData();
            $themeList[] = $info;
        }

        return $themeList;
    }

    public function ring($noise = 0)
    {
        $ring = (new RingModel())->ringList($noise);

        $ringList = [];

        foreach ($ring as $value){
            $info = $value->getData();
            $info['ringUrl'] = urlCompletion($info['ringUrl']);
            $ringList[] = $info;
        }

        return $ringList;
    }

    public function settingInfo($imei = '')
    {
        $configureModel = new ConfigureModel();
        $setting = $configureModel->settingInfo($imei);
        if($setting){
            $response = $setting->getData();
            $response['taskRingUrl'] = urlCompletion($response['taskRingUrl']);
            $response['restRingUrl'] = urlCompletion($response['restRingUrl']);
            $response['noiseRingUrl'] = urlCompletion($response['noiseRingUrl']);
            $response['taskColour'] = $response['taskColour'] ? $response['taskColour'] : "无";
            $response['restColour'] = $response['restColour'] ? $response['restColour'] : "无";
            $response['taskFinishRing'] = $response['taskFinishRing'] ? $response['taskFinishRing'] : "无";
            $response['restFinishRing'] = $response['restFinishRing'] ? $response['restFinishRing'] : "无";
            $response['noiseRing'] = $response['noiseRing'] ? $response['noiseRing'] : "无";
        }else{
			$ringModel = new RingModel();
			$ring = $ringModel->ringGetFirst(0);
			$noise = $ringModel->ringGetFirst(1);
            $response['theme'] = 0;
            $response['taskColour'] = '无';
            $response['restColour']  = '无';
            $response['taskFinish'] = !empty($ring['ringId']) ? $ring['ringId'] : 0;
            $response['taskFinishRing'] = !empty($ring['ringName']) ? $ring['ringName'] : "无";
            $response['restFinish'] = !empty($ring['ringId']) ? $ring['ringId'] : 0;
            $response['restFinishRing'] = !empty($ring['ringName']) ? $ring['ringName'] : "无";
            $response['noise'] = !empty($noise['ringId']) ? $noise['ringId'] : 0;
            $response['noiseRing'] = !empty($noise['ringName']) ? $noise['ringName'] : "无";

			$response['taskRingUrl'] = !empty($ring['ringUrl']) ? urlCompletion($ring['ringUrl']) : "";
            $response['restRingUrl'] = !empty($ring['ringUrl']) ? urlCompletion($ring['ringUrl']) : "";
            $response['noiseRingUrl'] = !empty($noise['ringUrl']) ? urlCompletion($noise['ringUrl']) : "";

            $response['taskTime'] = 25;
            $response['sortRest'] = 5;
            $response['longRest'] = 15;
            $response['taskNum'] = 4;
            $response['autoNext'] = 0;
            $response['screenOn'] = 0;
            $response['shockOn'] = 1;
            $response['strict'] = 1;
            try{
                $configureModel->create(['imei' => $imei, 'taskFinish' => $response['taskFinish'], 'restFinish' => $response['restFinish'], 'noise' => $response['noise']]);
            }catch (Exception $e){
                Log::error("settingInfo error:" . $e->getMessage());
                return [];
            }
        }
        return $response;
    }

    public function setting($param = [])
    {
        $imei = $param['imei'];
        unset($param['imei']);

        try{
            (new ConfigureModel())->save($param, ['imei' => $imei]);
        }catch (Exception $e){
            Log::error("setting error:" . $e->getMessage());
            return false;
        }
        return true;
    }
}