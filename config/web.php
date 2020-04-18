<?php


$config = [
    'id' => 'web',
    'homeUrl' => '/',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'main/index',
    'bootstrap' => [
        'plugins',
        'panix\engine\plugins\goaway\GoAway',
        //'webcontrol'
    ],
    'components' => [
        'stats' => ['class' => 'panix\mod\stats\components\Stats'],
        'geoip' => ['class' => 'panix\engine\components\geoip\GeoIP'],
        //'webcontrol' => ['class' => 'panix\engine\widgets\webcontrol\WebInlineControl'],
        'view' => [
            'class' => 'panix\mod\plugins\components\View',
            'as Layout' => [
                'class' => 'panix\engine\behaviors\LayoutBehavior',
            ],
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    //'cachePath' => '@runtime/Smarty/cache',
                ],
            ],
            'theme' => [
                'class' => 'panix\engine\base\Theme'
            ],
        ],
        'request' => [
            'class' => 'panix\engine\WebRequest',
        ],

        'errorHandler' => [
            //'class'=>'panix\engine\base\ErrorHandler'
            //'errorAction' => 'site/error',
            'errorAction' => 'main/error',
            // 'errorView' => '@webroot/themes/basic/views/layouts/error.php'
        ],

    ],
    //'on beforeRequest' => ['class' => 'panix\engine\base\ThemeView']
    /*'as access' => [
        'class' => panix\mod\rbac\filters\AccessControl::class,
        'allowActions' => [
            '/*',
            'admin/*',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
    ],*/

];

return $config;
