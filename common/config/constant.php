<?php

//域名常量配置
define('DS', DIRECTORY_SEPARATOR);

//自动加载全局函数，仅供测试
if(YII_DEBUG){
    /**
     * 高亮打印调试信息
     * @param $data
     */
    function pr($data,$exit=true){
        \yii\helpers\VarDumper::dump($data,10,true);
        if($exit) exit;
    }

    function prjson($data, $exit=true){
        $json = json_encode($data);
        echo $json;
        if($exit) die();
    }
}