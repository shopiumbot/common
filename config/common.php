<?php


$date = new \DateTime(date('Y-m-d', time()), new \DateTimeZone('Europe/Kiev'));
$logDate = $date->format('Y-m-d');
use yii\web\UrlNormalizer;


$config = [
    'name' => 'ShopiumBot',
    'basePath' => dirname(__DIR__) . '/../',
    'language' => 'ru',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    //'sourceLanguage'=>'ru',
    // 'runtimePath' => '@app/backend/runtime',
    'controllerNamespace' => 'panix\engine\controllers',
    //'defaultRoute' => 'site/index',
    'bootstrap' => [
        'log',
        'panix\engine\BootstrapModule'
    ],
    'controllerMap' => [
        'site' => 'panix\engine\controllers\WebController',
        'badmin' => 'panix\engine\controllers\AdminController',
    ],
    'modules' => [
        'admin' => ['class' => 'shopium\mod\admin\Module'],
        'rbac' => [
            'class' => 'panix\mod\rbac\Module',
            //'as access' => [
            //    'class' => panix\mod\rbac\filters\AccessControl::class
            //],
        ],
        'telegram' => ['class' => 'shopium\mod\telegram\Module'],
        'user' => ['class' => 'panix\mod\user\Module'],
        'shop' => ['class' => 'core\modules\shop\Module'],
        'contacts' => ['class' => 'core\modules\contacts\Module'],
        'discounts' => ['class' => 'shopium\mod\discounts\Module'],
        'csv' => ['class' => 'shopium\mod\csv\Module'],
        'images' => ['class' => 'core\modules\images\Module'],
        'cart' => ['class' => 'shopium\mod\cart\Module'],
    ],
    'components' => [
		'telegram' => ['class' => 'shopium\mod\telegram\components\Telegram'],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],
        'img' => [
            'class' => 'panix\engine\components\ImageHandler',
        ],
        'geoip' => ['class' => 'panix\engine\components\geoip\GeoIP'],
        'formatter' => ['class' => 'panix\engine\i18n\Formatter'],
        'assetManager' => [
            'forceCopy' => YII_DEBUG,
            'appendTimestamp' => true
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'default' => 'default.php',
                    ],
                ],
                'app/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/panix/engine/messages',
                    'fileMap' => [
                        'app/default' => 'default.php',
                        'app/admin' => 'admin.php',
                        'app/month' => 'month.php',
                        'app/error' => 'error.php',
                        'app/geoip_country' => 'geoip_country.php',
                        'app/geoip_city' => 'geoip_city.php',
                    ],
                ],
            ],
        ],
        'session' => [
            'class' => '\panix\engine\web\DbSession',
            'timeout' => 3600
            //'class' => '\yii\web\DbSession',
            //'writeCallback'=>['panix\engine\web\DbSession', 'writeFields']
        ],
        'cache' => [
            'directoryLevel' => 0,
            'keyPrefix' => '',
            'class' => 'yii\caching\FileCache', //DummyCache
        ],
        'user' => [
            'class' => 'panix\mod\user\components\WebUser',
            // 'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'mailer' => [
            'class' => 'panix\engine\Mailer',
            'htmlLayout' => 'layouts/html'
            //  'class' => 'yii\swiftmailer\Mailer',
        ],
        'log' => ['class' => 'panix\engine\log\Dispatcher'],
        'settings' => ['class' => 'panix\engine\components\Settings'],
        'urlManager' => [
            //'class' => 'panix\engine\ManagerUrl',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'baseUrl' => '',
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            ],
            'rules' => [
                '' => 'site/index',
                'placeholder' => 'site/placeholder',
                //'/admin' => 'admin/admin/default/index',
                // 'admin/auth' => 'admin/auth/index',
                // ['pattern' => 'admin/app/<controller:\w+>', 'route' => 'admin/admin/<controller>/index'],
                //['pattern' => 'admin/app/<controller:\w+>/<action:[0-9a-zA-Z_\-]+>', 'route' => 'admin/admin/<controller>/<action>'],
                //  ['pattern' => 'admin/<module:\w+>/<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>', 'route' => '<module>/admin/<controller>/<action>'],
                //['pattern' => 'admin/<module:\w+>', 'route' => '<module>/admin/default/index'],
                //['pattern' => 'admin/<module:\w+>/<controller:[0-9a-zA-Z_\-]+>', 'route' => '<module>/admin/<controller>/index'],
                //['pattern' => 'admin/<module:\w+>/<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>/<page:\d+>', 'route' => '<module>/admin/<controller>/<action>'],
               // 'http://demo.shopium24.loc/admin' => 'admin/admin/default/index',

                ['pattern' => 'admin/auth', 'route' => 'admin/auth/index'],
                ['pattern' => 'admin', 'route' => 'admin/admin/default/index'],

                ['pattern' => 'admin/app/<controller:[0-9a-zA-Z_\-]+>', 'route' => 'admin/admin/<controller>/index'],
                ['pattern' => 'admin/app/<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>', 'route' => 'admin/admin/<controller>/<action>'],
                ['pattern' => 'admin/<module:\w+>/<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>', 'route' => '<module>/admin/<controller>/<action>'],
                ['pattern' => 'admin/<module:\w+>', 'route' => '<module>/admin/default/index'],
                ['pattern' => 'admin/<module:\w+>/<controller:[0-9a-zA-Z_\-]+>', 'route' => '<module>/admin/<controller>/index'],
                ['pattern' => 'admin/<module:\w+>/<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>/<page:\d+>', 'route' => '<module>/admin/<controller>/<action>'],

            ],
        ],
        'db' => [
            'class' => 'panix\engine\db\Connection',
            'charset' => 'utf8', //utf8 на utf8mb4. FOR Emoji
            'serverStatusCache' => YII_DEBUG ? 0 : 3600,
            'schemaCacheDuration' => YII_DEBUG ? 0 : 3600 * 24,
            'enableSchemaCache' => true,
            'schemaCache' => 'cache'
            //'on afterOpen' => function($event) {
            //$event->sender->createCommand("SET time_zone = '" . date('P') . "'")->execute();
            //$event->sender->createCommand("SET names utf8")->execute();
            //},
        ],
    ],

    'params' => require(__DIR__ . '/params.php'),
];


return $config;