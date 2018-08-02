<?php
return [
    'components' => [
        'db' => require(__DIR__ . '/db-prod.php'),
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'cache' => [
            'class' => 'common\components\Cache',
            'keyPrefix'   => 'yii:gt233:datacache:',
            'redis' => [
                'hostname' => 'localhost',
                'password' => '',#本地配置会覆盖线上配置
                'port' => 6379,
                'database' => 0,
            ]
        ],
        'file_cache' => [
            'class' => 'yii\caching\FileCache',
            //'keyPrefix' => 'fileCache_gt233:',
            'directoryLevel' => 2,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'password' => '',#本地配置会覆盖线上配置
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
