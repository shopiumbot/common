<?php

error_reporting(E_ALL);
//Timezone
date_default_timezone_set("UTC");

// comment out the following two lines when deployed to production
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    $env = 'dev';
    $debug = true;
} else {
    $env = 'prod';
    $debug = false;

}
die('d');
defined('YII_DEBUG') or define('YII_DEBUG', $debug);
defined('YII_ENV') or define('YII_ENV', $env);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';


$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/config/common.php',
    require __DIR__ . '/config/web.php'
);



$app = new \panix\engine\WebApplication($config);
$app->run();