<?php

namespace app\modules\shop\widgets\filtersnew;

use panix\engine\web\AssetBundle;

/**
 * Class FilterAsset
 * @package app\modules\shop\widgets\filtersnew\assets
 */
class FilterAsset extends AssetBundle
{

    public $sourcePath = __DIR__.'/assets';

    public $js = [
        'js/filter.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
