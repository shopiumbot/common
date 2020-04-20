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
    <div id="header-top">
        <div class="container">
            <nav class="navbar-expand">
                <div class="navbar-collapse">


                </div>
            </nav>
        </div>

    </div>
    <div class="container" id="header-center">
        <div class="row">
            <div class="col-lg-3 col-md-6 d-flex align-items-center">
                <a class="navbar-brand ml-auto mr-auto mb-3 mb-md-0" href="/"></a>
            </div>
            <div class="col-lg-2 col-md-6 d-flex align-items-center">
                <div class="header-phones ml-auto mr-auto mb-3 mb-md-0">

                </div>
            </div>

        </div>
    </div>
    <nav class="navbar navbar-expand-lg">
        <div class="container megamenu">

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
                    aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>
    </nav>

</header>
