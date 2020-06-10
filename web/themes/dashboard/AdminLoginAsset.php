<?php

namespace core\web\themes\dashboard;

use yii\web\AssetBundle;

/**
 * Class AdminLoginAsset
 * @package app\web\themes\dashboard
 */
class AdminLoginAsset extends AssetBundle {


    public function init()
    {
        $this->sourcePath = \Yii::$app->view->theme->basePath . '/assets';
        parent::init();
    }
    public $jsOptions = array(
        'position' => \yii\web\View::POS_END
    );
    public $css = [
        'css/login.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'core\web\themes\dashboard\AdminAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'panix\engine\assets\CommonAsset'
    ];

}
