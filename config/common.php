<?php


$date = new \DateTime(date('Y-m-d', time()), new \DateTimeZone('Europe/Kiev'));
$logDate = $date->format('Y-m-d');
use yii\web\UrlNormalizer;


$config = [
    'name' => 'Shopium24',
    'basePath' => dirname(__DIR__) . '/../',
    'language' => 'ru',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@uploads' => '@app/web/uploads',
    ],
    //'sourceLanguage'=>'ru',
    // 'runtimePath' => '@app/backend/runtime',
    'controllerNamespace' => 'panix\engine\controllers',
    //'defaultRoute' => 'main/index',
    'bootstrap' => [
        'log',
        'maintenanceMode',
        'panix\engine\widgets\webcontrol\WebInlineControl',
        'panix\engine\BootstrapModule'
    ],
    'controllerMap' => [
        'main' => 'panix\engine\controllers\WebController',
        'badmin' => 'panix\engine\controllers\AdminController',
    ],
    'modules' => [
        'admin' => ['class' => 'panix\mod\admin\Module'],
        'plugins' => [
            'class' => 'panix\mod\plugins\Module',
            'pluginsDir' => [
                '@panix/engine/plugins',
            ]
        ],
        'rbac' => [
            'class' => 'panix\mod\rbac\Module',
            //'as access' => [
            //    'class' => panix\mod\rbac\filters\AccessControl::class
            //],
        ],

        'user' => ['class' => 'panix\mod\user\Module'],
        //'presentation' => ['class' => 'panix\mod\presentation\Module'],
        'compare' => ['class' => 'panix\mod\compare\Module'],
        'shop' => ['class' => 'panix\mod\shop\Module'],
        //'shop' => ['class' => 'app\modules\shop\Module'],
        'sitemap' => ['class' => 'panix\mod\sitemap\Module'],
        'banner' => ['class' => 'panix\mod\banner\Module'],
        // 'sendpulse' => ['class' => 'panix\mod\sendpulse\Module'],
        'contacts' => ['class' => 'panix\mod\contacts\Module'],
        'seo' => ['class' => 'panix\mod\seo\Module'],
        'discounts' => ['class' => 'panix\mod\discounts\Module'],
        'comments' => ['class' => 'panix\mod\comments\Module'],
        'wishlist' => ['class' => 'panix\mod\wishlist\Module'],
        'exchange1c' => ['class' => 'panix\mod\exchange1c\Module'],
        'csv' => ['class' => 'panix\mod\csv\Module'],
        'yandexmarket' => ['class' => 'panix\mod\yandexmarket\Module'],
        'delivery' => ['class' => 'panix\mod\delivery\Module'],
        'images' => ['class' => 'panix\mod\images\Module'],
        //'forum' => ['class' => 'panix\mod\forum\Module'],
        'cart' => ['class' => 'panix\mod\cart\Module'],
        'pages' => ['class' => 'panix\mod\pages\Module'],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],
        'img' => [
            'class' => 'panix\engine\components\ImageHandler',
        ],
        /*'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AIzaSyAbeTCpxK7OGu_lXZDSnJjV1ItkUwPOBbc', // Server API Key (you can get it here: https://firebase.google.com/docs/server/setup#prerequisites)
        ],*/
        'sitemap' => [
            'class' => 'app\modules\sitemap\Sitemap',
            'models' => [
                // your models
                'app\modules\news\models\News',
                // or configuration for creating a behavior
                [
                    'class' => 'app\modules\news\models\News',
                    'behaviors' => [
                        'sitemap' => [
                            'class' => '\app\modules\sitemap\behaviors\SitemapBehavior',
                            'scope' => function ($model) {
                                /** @var \yii\db\ActiveQuery $model */
                                $model->select(['url', 'lastmod']);
                                $model->andWhere(['is_deleted' => 0]);
                            },
                            'dataClosure' => function ($model) {
                                /** @var self $model */
                                return [
                                    'loc' => \yii\helpers\Url::to($model->url, true),
                                    'lastmod' => strtotime($model->lastmod),
                                    'changefreq' => \panix\mod\sitemap\Module::DAILY,
                                    'priority' => 0.8
                                ];
                            }
                        ],
                    ],
                ],
            ],
            'urls' => [
                // your additional urls
                [
                    'loc' => ['/news/default/index'],
                    //'changefreq' => \app\modules\sitemap\Sitemap::DAILY,
                    'priority' => 0.8,
                    'news' => [
                        'publication' => [
                            'name' => 'Example Blog',
                            'language' => 'en',
                        ],
                        'access' => 'Subscription',
                        'genres' => 'Blog, UserGenerated',
                        'publication_date' => 'YYYY-MM-DDThh:mm:ssTZD',
                        'title' => 'Example Title',
                        'keywords' => 'example, keywords, comma-separated',
                        'stock_tickers' => 'NASDAQ:A, NASDAQ:B',
                    ],
                    'images' => [
                        [
                            'loc' => 'http://example.com/image.jpg',
                            'caption' => 'This is an example of a caption of an image',
                            'geo_location' => 'City, State',
                            'title' => 'Example image',
                            'license' => 'http://example.com/license',
                        ],
                    ],
                ],
            ],
            'enableGzip' => true, // default is false
            'cacheExpire' => 1, // 1 second. Default is 24 hours,
            'sortByPriority' => true, // default is false
        ],
        'curl' => ['class' => 'panix\engine\Curl'],
        'consoleRunner' => [
            'class' => 'panix\engine\components\ConsoleRunner',
            'file' => '@my/path/to/yii' // or an absolute path to console file
        ],
        'geoip' => ['class' => 'panix\engine\components\geoip\GeoIP'],
        'formatter' => ['class' => 'panix\engine\i18n\Formatter'],
        'maintenanceMode' => [
            'class' => 'panix\engine\maintenance\MaintenanceMode',
            // Allowed roles
            //'roles' => [
            //    'admin',
            //],
            //Retry-After header
            // 'retryAfter' => 120 //or Wed, 21 Oct 2015 07:28:00 GMT for example
        ],
        'assetManager' => [
            'forceCopy' => YII_DEBUG,
            'appendTimestamp' => true
        ],
        'plugins' => [
            'class' => panix\mod\plugins\components\PluginsManager::class,
            'appId' => panix\mod\plugins\BasePlugin::APP_BACKEND,
            // by default
            'enablePlugins' => true,
            'shortcodesParse' => true,
            'shortcodesIgnoreBlocks' => [
                '<pre[^>]*>' => '<\/pre>',
                // '<div class="content[^>]*>' => '<\/div>',
            ]
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/panix/engine/messages',
                    'fileMap' => [
                        'app' => 'app.php',
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
        'request' => [
            'class' => 'panix\engine\WebRequest',
            'baseUrl' => '',
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
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1000 * 10,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['yii\db\*', 'panix\engine\db\*'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . $logDate . '/db_error.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . $logDate . '/error.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . $logDate . '/warning.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . $logDate . '/info.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['panix\engine\db\*'],
                    'levels' => ['info', 'trace'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . $logDate . '/trace_core_db.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['profile'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . $logDate . '/profile.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/trace.log',
                ],
                [
                    'class' => 'panix\engine\log\EmailTarget',
                    'levels' => ['error', 'warning'],
                    'enabled' => false,//YII_DEBUG,
                    'categories' => ['yii\base\*'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:400',
                        'yii\i18n\PhpMessageSource::loadMessages'
                    ],
                    /*'message' => [
                        'from' => ['log@pixelion.com.ua'],
                        'to' => ['dev@pixelion.com.ua'],
                        'subject' => 'Ошибки базы данных на сайте app',
                    ],*/
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'logTable' => '{{%log_error}}',
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:400',
                        'yii\i18n\PhpMessageSource::loadMessages'
                    ],
                ],
            ],
        ],
        'languageManager' => ['class' => 'panix\engine\ManagerLanguage'],
        'settings' => ['class' => 'panix\engine\components\Settings'],
        'urlManager' => [
            'class' => 'panix\engine\ManagerUrl',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'baseUrl' => '',
            //'normalizer' => [
            //    'class' => 'yii\web\UrlNormalizer',
            //    'action' => UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            //],
            'rules' => [
                '' => 'main/index',
                'placeholder' => 'main/placeholder',
                //'/admin' => 'admin/admin/default/index',
                // 'admin/auth' => 'admin/auth/index',
                ['pattern' => 'like/<type:(up|down)>/<id:\d+>', 'route' => 'main/like'],
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
            'charset' => 'utf8mb4', //utf8 на utf8mb4. FOR Emoji
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