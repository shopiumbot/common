<?php

namespace core\web\themes\dashboard;

use yii\web\AssetBundle;

/**
 * Class AdminErrorAsset
 * @package app\web\themes\dashboard
 */
class AdminErrorAsset extends AssetBundle {


    public function init()
    {
        $this->sourcePath = \Yii::$app->view->theme->basePath . '/assets';
        parent::init();
    }
    public $jsOptions = array(
        'position' => \yii\web\View::POS_END
    );
    public $css = [
        'css/error.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'core\web\themes\dashboard\AdminAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'panix\engine\assets\CommonAsset'
    ];

}
