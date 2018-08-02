<?php
/**
 * redis cache 管理
 */
namespace common\components;

use Yii;
use yii\helpers\StringHelper;

class Cache extends \yii\redis\Cache
{
    /**
     * 重写 buildKey 检查key的合法性  // Helper::cache(key) 处理多项目时不理想待优化
     * @param mixed $key
     * @return string
     * @throws \Exception
     */
    public function buildKey($key)
    {
        /**
         * 只处理数据缓存，页面缓存，片段缓存不做处理
         * 包含有yii字符的将不做处理
         */
        if(is_string($key) && stripos($key,'yii')===false){
            $file =   Yii::getAlias('@common').'/config/cacheKey.php';
            $keyMap = require $file;
            $tmp = [];
            foreach ($keyMap as $k=>$v){
                foreach ($v as $k2=>$v2){
                    $tmp[$k.$k2] = $v2;
                }
            }
            if(YII_DEBUG && count(array_unique($tmp))!=count($tmp)){
                throw new \Exception($key.'缓存配置文件有重复value,配置文件:'.$file);
            }
            if(!isset($tmp[$this->keyPrefix.$key])){
                throw new \Exception($key.'不存在的key配置，请先配置缓存key,配置文件:'.$file);
            }else{
                $key = $tmp[$this->keyPrefix.$key];
            }
            $key = ctype_alnum($key) && StringHelper::byteLength($key) <= 32 ? $key : md5($key);
        }else if(is_array($key) || is_object($key)){
            $key = md5(json_encode($key));
        }
        return $this->keyPrefix . $key;
    }

}