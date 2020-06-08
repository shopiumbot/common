<?php


$config = [
    'id' => 'web',
    'homeUrl' => '/',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'site/index',
    'bootstrap' => [
        //'webcontrol'
    ],
    'components' => [
        'stats' => ['class' => 'panix\mod\stats\components\Stats'],
        'geoip' => ['class' => 'panix\engine\components\geoip\GeoIP'],
        //'webcontrol' => ['class' => 'panix\engine\widgets\webcontrol\WebInlineControl'],
        'view' => [
            'renderers' => [
                'tpl' => [
                    'class' => 'yii\smarty\ViewRenderer',
                    //'cachePath' => '@runtime/Smarty/cache',
                ],
            ],
            'theme' => [
                'class' => 'panix\engine\base\Theme',
				'basePath'=>'@core/web/themes',
                'name' => 'basic'
            ],
        ],
        'request' => [
           // 'class' => 'panix\engine\WebRequest',
            'baseUrl' => '',
        ],
        'errorHandler' => [
            //'class'=>'panix\engine\base\ErrorHandler'
            'errorAction' => 'site/error',
            //'errorView' => '@app/web/themes/basic/views/layouts/error.php'
        ],

    ],

];

return $config;
