<?php

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    //'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue', 'panix\engine\BootstrapModule'], //'telegram',
    'controllerNamespace' => 'app\commands',
    'language' => 'ru',
    'aliases' => [
        '@uploads' => '@app/web/uploads',
    ],
    'controllerMap' => [
        'sitemap' => [
            'class' => 'panix\mod\sitemap\console\CreateController',
        ],
        'migrate' => ['class' => 'panix\engine\console\controllers\MigrateController',
            //'migrationPath' => '@console/migrations',
            'migrationNamespaces' => [
                //  'console\migrations',
                // 'lo\plugins\migrations',
                //'app\migrations',
                // 'yii\rbac\migrations'
            ],
            'migrationPath' => ['@app/migrations', '@yii/rbac/migrations'], //,'@vendor/panix/mod-rbac/migrations'
        ]
    ],
    'components' => [
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            // Индивидуальные настройки драйвера
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],
        'urlManager' => require(__DIR__ . '/urlManager.php'),
        'settings' => ['class' => 'panix\engine\components\Settings'],
        'cache' => ['class' => 'yii\caching\FileCache'],
        'languageManager' => ['class' => 'panix\engine\ManagerLanguage'],
        //'urlManager' => require(__DIR__ . '/urlManager.php'),
        'db' => require(__DIR__ . '/../config/db_local.php'),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1000 * 10,
            'targets' => [
                'file1' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/console_db_error.log',
                    'categories' => ['yii\db\*']
                ],
                'file2' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/console_error.log',
                    // 'categories' => ['yii\db\*']
                ],
                'file3' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/console_warning.log',
                ],
                'file4' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/console_info.log',
                ],
                /*[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['profile'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/profile.log',
                ],*/
                /*[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/' . date('Y-m-d') . '/trace.log',
                ],*/
            ],
        ],
    ],
    'params' => require(__DIR__ . '/../config/params.php'),
];
