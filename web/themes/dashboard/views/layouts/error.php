<?php

use panix\engine\Html;
use core\web\themes\dashboard\AdminErrorAsset;

AdminErrorAsset::register($this);


?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <title><?= $this->context->view->title; ?></title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
    </head>
    <body class="error-page">
    <?php $this->beginBody() ?>
    <div class="container">
        <div class="row">
            <div id="login-container" style="margin-top:80px;"
                 class="animate__animated <?php if (!Yii::$app->session->hasFlash('error')) { ?>animate__bounceInDown<?php } ?> col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-lg-8 offset-lg-2">


                        <?= $content ?>

                <div class="text-center copyright">&copy; 2019-<?= date('Y'); ?>
                    &laquo;<?= Html::a('ShopiumBot', ['https://shopiumbot.com']); ?>&raquo;
                </div>
            </div>
        </div>
    </div>


    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>