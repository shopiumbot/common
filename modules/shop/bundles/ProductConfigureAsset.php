<?php

namespace core\modules\shop\bundles;

use panix\engine\web\AssetBundle;

/**
 * Class WebAsset
 * @package core\modules\shop\assets
 */
class ProductConfigureAsset extends AssetBundle
{

    public $sourcePath = __DIR__.'/../assets';

    public $js = [
        'js/product.view.configurations.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
