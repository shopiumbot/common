<?php

namespace core\modules\shop\bundles\admin;

use panix\engine\web\AssetBundle;

/**
 * Class ConfigurationsAsset
 * @package core\modules\shop\bundles\admin
 */
class ConfigurationsAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../../assets/admin';
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD //require for products.js   POS_END
    );
    public $js = [
        'js/products.configurations.js',
    ];
    public $depends = [
        '\yii\web\JqueryAsset',
    ];
}
