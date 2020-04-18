<?php

/**
 *
 * @author Pixelion CMS <dev@pixelion.com.ua>
 * @link http://www.pixelion.com.ua/
 */

namespace core\modules\shop\bundles\admin;

use panix\engine\web\AssetBundle;

/**
 * Class ProductIndex
 * @package core\modules\shop\assets\admin
 */
class ProductIndex extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../../assets/admin';

    public $js = [
        'js/products.index.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\grid\GridViewAsset',
        'app\web\themes\dashboard\AdminAsset',
        //'app\web\themes\dashboard\ThemeCssAsset'

    ];
}
