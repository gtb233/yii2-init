<?php
/**
 * redis 统一key管理
 *
 * 使用:
 *
 * Tool::cache()->set('test',123,3600);
 * Tool::cache()->get('test');
 *
 * Tool::cache 将对key的唯一性做校验，没有在当前数组配置的key不可使用
 *
 * keyPrefix + key值 = 唯一key
 */
return [
    //默认keyPrefix 为空
    '' => [
        //key 名称 => key值
        'test' => 'tKey', //测试
        'express' => 'exp', //物流公司数组id=>name
    ],
    'category' => [
        'mall-00' => 'cm00',
        'mall-01' => 'cm01',
        'mall-02' => 'cm02',
        'mall-03' => 'cm03',
        'mall-10' => 'cm10',
        'mall-11' => 'cm11',
        'mall-12' => 'cm12',
        'mall-13' => 'cm13',
        'search'=>'cs',
        'cateNav'=>'cnt',
        'lanmu1'=>'lm',
    ],
    'region'=>[
        'china'=>'rc',
        'getChinaCities'=>'rct',
        'province'=>'rat',
        'chinaList'=>'cl',
    ],
    'top'=>[
        'topicData'=>'tops',
    ],
    'hotgoods'=>[
        'list'=>'hg',
    ],
    'payment'=>[
        'payList'=> 'pl',
    ],

];