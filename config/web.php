<?php
use \yii\web\Request;
$params = require(__DIR__ . '/params.php');
$baseUrl = str_replace('/web', '', (new Request)->getBaseUrl());

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'languagepicker'
    ],
    'name' => 'Prozorro',
    'timezone' => 'Europe/Kiev',
    'language' => 'uk-UA',
    'components' => [
        'session' => [
            'class' => 'yii\web\DbSession',
            'timeout' => 3600*12,
//             'db' => 'mydb',  // ID компонента для взаимодействия с БД. По умолчанию 'db'.
             'sessionTable' => 'session',
        ],
        'request' => [
            'baseUrl' => $baseUrl,
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'T-9-y5SmF3nofWVF2fPpcWM4-VvHbDd3',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            // 'enableAutoLogin' => false,
            // 'authTimeout'=>3600,
            'loginUrl' => ['/'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.ukraine.com.ua',
                'username' => 'test@byustudio.in.ua',
                'password' => '',
                'port' => '25',
                // 'encryption' => 'tls',
            ],
        ],
        'VarDumper' => [
//            'class' => 'yii\helpers\VarDumper',
            'class' => 'app\components\MyVarDumper',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],//, 'profile'
                ],
                // [
                //     // 'class' => 'yii\log\FileTarget',
                //     // 'logFile' => '@runtime/logs/sql.log',
                //     'class' => 'yii\log\DbTarget',
                //     'logTable'=>'request_logs',
                //     'logVars' => [],
                //     'levels' => ['profile'],
                //     'categories' => ['yii\db\Command::query', 'yii\db\Command::execute'],
                //     'prefix' => function($message) {
                //         return '';
                //     }
                // ]
                // [
                //     'class' => 'yii\log\DbTarget',
                //     'logTable'=>'request_logs',
                //     'logVars' => ['responce'],
                //     'levels' => ['error', 'warning','profile'],
                // ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'baseUrl' => $baseUrl,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                'cabinet/<id:\d+>' => 'cabinet/index',
                'tender/view/<id:[A-Za-z0-9 -_\-]+>' => 'tender/view',
                '<module:\w+>/tender/view/<id:[A-Za-z0-9 -_\-]+>' => '<module>/tender/view',

                'pages/manager/' => 'pages/manager/',
                'pages/pages-tree/<action:[-\w]+>/<id:\d+>' => 'pages/pages-tree/<action>',
                'pages/pages-tree/<action:[-\w]+>' => 'pages/pages-tree/<action>',
                'pages/manager/<action:[-\w]+>' => 'pages/manager/<action>',
                'pages/manager/<action:[-\w]+>/<id:\d+>' => 'pages/manager/<action>',
                'pages/<page:[\w-/]+>' => 'pages/default/index',

                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ]

        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'forceCopy' => false,
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'forceTranslation' => true,
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
//                    'sourceLanguage' => 'uk-UA',
                    'fileMap' => [
                        'app' => 'app.php',
                        'backend' => 'backend.php',
                        // 'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'languagepicker' => [
            'class' => 'app\components\LanguageSwitcher',
            'languages' => ['uk-UA','en-US'],
//            'languages' => ['uk-UA'],
            'cookieName' => 'language',
        ],
        'opAPI' => require(__DIR__ . '/api.php'),
        /*'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'author',], //delegate //authorized
        ],*/
         'finance' => [
            'class' => 'app\components\finance',
        ],
        'formatter' => [
            'defaultTimeZone' => 'Europe/Kiev',
            'locale' => 'uk-UA'
        ],
    ],
    'modules' => [
        'utility' => [
            'class' => 'c006\utility\migration\Module',
            'as access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \app\models\User::checkAdmin();
                        }
                    ],
                ],
            ],
        ],
        'backend' => [
            'class' => 'app\modules\backend\Backend',
            'as access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \app\models\User::checkAdmin();
                        }
                    ],
                ],
            ],
        ],
        'pages' => [
            'class' => 'app\modules\pages\Module',
            'tableName' => '{{%pages}}',
            'controllerMap' => [
                'manager' => [
                    'class' => 'app\modules\pages\controllers\ManagerController',
                    'as access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'rules' => [
                            [
                                'allow' => true,
                                // 'roles' => ['admin'],
                                'roles' => ['@'],
                                'matchCallback' => function ($rule, $action) {
                                    return \app\models\User::checkAdmin();
                                }
                            ],
                        ],
                    ],
                ],
            ],
            'imperaviLanguage' => 'ua',
            'pathToImages' => '@webroot/uploads/pages/images',
            'urlToImages' => '@web/uploads/pages/images',
            'pathToFiles' => '@webroot/uploads/pages/files',
            'urlToFiles' => '@web/uploads/pages/files',
            'uploadImage' => true,
            'uploadFile' => true,
            'addImage' => true,
            'addFile' => true,
        ],
        'seller' => [
            'class' => 'app\modules\seller\Seller',
            'defaultRoute' => 'cabinet',
            'layout' => 'main',
            'viewPath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views',
            'as access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        // 'ips'=>['91.225.165.4'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \app\models\Companies::checkCompanyIsSeller() || \app\models\User::checkAdmin();
                        }
                    ],
                ],
            ],
        ],
        'buyer' => [
            'class' => 'app\modules\buyer\Buyer',
            'defaultRoute' => 'cabinet',
            'viewPath' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views',
            'layout' => 'main',
            'as access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        // 'ips'=>['91.225.165.4'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            // return true;
                            return \app\models\Companies::checkCompanyIsBuyer() || \app\models\User::checkAdmin();
                        }
                    ],
                ],
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
//        'allowedIPs' => ['127.0.0.1', '::1', '91.225.165.4', '94.154.232.2'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
//        'allowedIPs' => ['127.0.0.1', '::1', '91.225.165.4', '94.154.232.2'],
    ];
}

return $config;
