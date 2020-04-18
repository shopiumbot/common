<?php
/**
 *
 * @author Pixelion CMS <dev@pixelion.com.ua>
 * @link http://www.pixelion.com.ua/
 */
namespace app\modules\shop\bundles\admin;


use panix\engine\web\AssetBundle;

class ProductAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/../../assets/admin';

    public $js = [
        'js/products.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
