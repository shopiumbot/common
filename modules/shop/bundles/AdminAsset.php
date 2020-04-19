<?php
/**
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 */

namespace core\modules\shop\bundles;

use panix\engine\web\AssetBundle;

/**
 * Class AdminAsset
 * @package core\modules\shop\assets
 */
class AdminAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/../assets/admin';
    public $js = [
        'js/products.js',
        // 'js/products.index.js',
    ];

    /* public $depends = [
           'panix\engine\assets\TinyMceAsset'
       ];*/
}
