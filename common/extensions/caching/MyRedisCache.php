<?php
namespace common\extensions\caching;

use yii\redis\Cache;
use yii\helpers\StringHelper;

/**
 * 扩展REDIS CACHE 添加前缀
 * Add a property 'hashPrefixKey' to keep prefixKey without hashing.
 * ~~~
 * [
 *     'components' => [
 *         'cache' => [
 *             'class' => 'yii\redis\Cache',
 *             'keyPrefix'   => 'dbcache:',
 *             'redis' => [
 *                 'hostname' => 'localhost',
 *                 'password' => 'password',
 *                 'port' => 6379,
 *                 'database' => 0,
 *             ]
 *         ],
 *     ],
 * ]
 * ~~~
 */
class MyRedisCache extends Cache
{
    /**
     * 键处理
     * @inheritdoc
     */
    public function buildKey($key)
    {
        if (is_string($key)) {
            //全数字或字母 且字节小于32则不使用 md5
            $key = ctype_alnum($key) && StringHelper::byteLength($key) <= 32 ? $key : md5($key);
        } else {
            $key = md5(json_encode($key));
        }
    
        return $this->keyPrefix . $key;
    }
    
    /**
     * Deletes specified|all values from cache.
     * This is the implementation of the method declared in the parent class.
     * @return boolean whether the flush operation was successful.
     */
    public function flushValues($keyPrefix=NULL)
    {
        if($keyPrefix){
            //Delete the keys with prefix specified by $keyPrefix.
            $pattern = "{$keyPrefix}*";
            if($keys = $this->redis->executeCommand('KEYS', [$pattern]) ){
                foreach($keys as $key){
                    $this->deleteValue($key);
                }
            }
            return true;
        }
        return $this->redis->executeCommand('FLUSHDB');
    }
}
