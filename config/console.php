<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::setAlias('@root', dirname(__DIR__));

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'timezone' => 'Europe/Kiev',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'gii',
    ],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'language' => 'uk-UA',
    'components' => [
//        'cache' => [
//            'class' => 'yii\caching\FileCache',
//        ],

        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'], //,'profile'
                    'logFile' => '@app/runtime/logs/console.log',
                ],
//                [
//                    'class' => 'yii\log\FileTarget',
//                    'logFile' => '@runtime/logs/console_sql.log',
////                    'class' => 'yii\log\DbTarget',
////                    'logTable'=>'request_logs',
//                    'logVars' => [],
//                    'levels' => ['profile'],
//                    'categories' => ['yii\db\Command::query', 'yii\db\Command::execute'],
//                    'prefix' => function($message) {
//                        return '';
//                    }
//                ]
            ],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'forceTranslation' => true,
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
//                    'sourceLanguage' => 'uk-UA',
                    'sourceLanguage' => 'ru',
                    'fileMap' => [
                        'app' => 'app.php',
                        'backend' => 'backend.php',
                        // 'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.ukraine.com.ua',
                'username' => 'test@byustudio.in.ua',
                'password' => 'Dnmb3iV482TR',
                'port' => '25',
                // 'encryption' => 'tls',
            ],
        ],
        'db' => $db,
        'opAPI' => require(__DIR__ . '/api.php'),
        'finance' => [
            'class' => 'app\components\finance',
        ],
    ],
    'params' => $params,
];
