<?php

namespace core\modules\shop\bundles;

use panix\engine\web\AssetBundle;

/**
 * Class WebAsset
 * @package core\modules\shop\assets
 */
class NotifyAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../assets';

    public $js = [
        'js/notify.js',
    ];

    public $depends = [
        'panix\mod\cart\CartAsset',
        'panix\mod\wishlist\WishlistAsset',
    ];

}
