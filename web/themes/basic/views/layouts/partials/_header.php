<?php
use panix\engine\Html;
use yii\helpers\Url;
use panix\engine\CMS;
use core\modules\shop\models\Category;

$this->registerJs("
    $(window).on('load', function () {
        var preloader = $('.loaderArea'),
            loader = preloader.find('.loader');
        loader.fadeOut();
        preloader.delay(350).fadeOut('slow');
    });

", \yii\web\View::POS_END, 'preloader-js');

$config = Yii::$app->settings->get('contacts');

?>

<div class="loaderArea d-none">
    <div class="loader">
        <div class="cssload-inner cssload-one"></div>
        <div class="cssload-inner cssload-two"></div>
        <div class="cssload-inner cssload-three"></div>
    </div>
</div>



<header>
    <div class="container" id="header-center">
        <div class="row">
            <div class="col-lg-12 col-md-12 d-flex align-items-center">
                <a class="navbar-brand ml-auto mr-auto mb-3 mb-md-0" href="/"></a>
            </div>
        </div>
    </div>
</header>
