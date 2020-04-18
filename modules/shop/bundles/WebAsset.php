<?php

namespace app\modules\shop\bundles;

use panix\engine\web\AssetBundle;

/**
 * Class WebAsset
 * @package app\modules\shop\assets
 */
class WebAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../assets';
    /*
      public $js = [
      'js/relatedProductsTab.js',
      'js/products.js',
      // 'js/products.index.js',
      ]; */

    public $js = [
        'js/switchCurrency.js',
    ];

    public $css = [
         'css/shop.css',
    ];

    public $depends = [
        'panix\mod\cart\CartAsset',
        'panix\mod\wishlist\WishlistAsset',
    ];

}
