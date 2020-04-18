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
defined('CORE_PATH') or define('CORE_PATH', __DIR__ . '/../../core');
defined('YII_DEBUG') or define('YII_DEBUG', $debug);
defined('YII_ENV') or define('YII_ENV', $env);

require CORE_PATH . '/vendor/autoload.php';
require CORE_PATH . '/vendor/yiisoft/yii2/Yii.php';


$config = yii\helpers\ArrayHelper::merge(
    require CORE_PATH . '/config/common.php',
    require CORE_PATH . '/config/web.php',
    require __DIR__ . '/../config/web.php'
);



$app = new \panix\engine\WebApplication($config);
$app->run();