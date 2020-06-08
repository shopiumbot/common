<?php

namespace core\web\themes\dashboard;

use panix\engine\web\AssetBundle;

/**
 * Class AdminAsset
 * @package app\web\themes\dashboard
 */
class AdminAsset extends AssetBundle
{


    public $css = [
        '//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=cyrillic',
        'css/chartist.min.css',
        'css/c3.min.css',
        'css/style.css',
    ];
    public $js = [
        'js/perfect-scrollbar.jquery.min.js',
        'js/app.js',
        'js/sidebarmenu.js',
        'js/custom.min.js',
        'js/dashboard.js',
        'js/chat.js',
    ];

    public $depends = [
       // 'panix\ext\fancybox\FancyboxAsset',
       // 'panix\engine\assets\CommonAsset',
       // 'panix\engine\assets\ClipboardAsset',
       // 'core\web\themes\dashboard\AdminCountersAsset',
        'panix\engine\assets\JqueryCookieAsset',
        'panix\engine\assets\TouchPunchAsset',
        'panix\engine\assets\CommonAsset',
    ];

    public $cssOptions = ['data-theme' => 1];

    public function init()
    {

        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
}
