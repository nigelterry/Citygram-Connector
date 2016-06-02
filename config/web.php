<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'devicedetect'],
    'defaultRoute' => 'site/home',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'ob8M9peUCFIA9rv9k2Mhuo_DquvVnd1n',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
//        'db' => require(__DIR__ . '/db.php'),
        'mongodb' => [
            'class' => '\yii\mongodb\Connection',
            'dsn' => 'mongodb://localhost:27017/citygram',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
	            '/' => 'site/home',
	            'site/<action:\w+>' => 'site/<action>',
	            '<controller:[\w-]+>/<action:[\w-]+>' => 'base/route',
	            '<controller:[\w-]+>' => 'base/route'
            ],
        ],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
        'formatter' => [
            'currencyCode' => '$',
        ]
    ],
    'modules' => [
        // ...
        'gii' => [
            'class' => 'yii\gii\Module',
            'generators' => [
                'mongoDbModel' => [
                    'class' => 'yii\mongodb\gii\model\Generator'
                ]
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
                'panels' => [
            'mongodb' => ['db' => 'mongodb', 'class' => 'yii\\mongodb\\debug\\MongoDbPanel'],
        ],
    ];

//    $config['bootstrap'][] = 'gii';
//    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
