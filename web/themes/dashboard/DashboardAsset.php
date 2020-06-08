<?php
/**
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 */

namespace app\modules\user;

use panix\engine\web\AssetBundle;

/**
 * Class AdminAsset
 * @package app\modules\shop\assets
 */
class DashboardAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';
    public $js = [
        'js/perfect-scrollbar.jquery.min.js',

         'js/app.js',

        'js/waves.js',
        'js/sidebarmenu.js',
        'js/custom.min.js',
        'js/dashboard.js',
        'js/chat.js',
    ];
    public $css = [
        'css/chartist.min.css',
        'css/c3.min.css',
        'css/style.css',
    ];
    public $depends = [
        'panix\engine\assets\JqueryCookieAsset',
        'panix\engine\assets\TouchPunchAsset',
        'panix\engine\assets\CommonAsset',
    ];
}
