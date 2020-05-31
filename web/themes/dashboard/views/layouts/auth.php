<?php

use yii\helpers\Html;
use core\web\themes\dashboard\AdminLoginAsset;
AdminLoginAsset::register($this);


$this->registerJs('
            $(function () {
                var h = $(\'.card\').height();
                var dh = $(window).height();
                $(\'#loginbox\').css({\'margin-top\': (dh / 2) - h});
                $(window).resize(function () {
                    var h = $(\'.card\').height();
                    var dh = $(window).height();
                    $(\'#loginbox\').css({\'margin-top\': (dh / 2) - h});
                });
                $(\'.auth-logo\').hover(function () {
                    // $(this).removeClass(\'zoomInDown\').addClass(\'swing\');
                }, function () {
                    //  $(this).removeClass(\'swing\');
                });
            });
', \yii\web\View::POS_END);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title><?= Yii::t('app/admin', 'ADMIN_PANEL'); ?></title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
    </head>
    <body class="no-radius1">
        <?php $this->beginBody() ?>

        <div class="container">
            <div class="row">
            <div id="login-container" style="margin-top:80px;"
                 class="animated <?php if (!Yii::$app->session->hasFlash('error')) { ?>bounceInDown<?php } ?> col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-lg-4 offset-lg-4">

                <div class="text-center auth-logo animated zoomInDown2 ">
                    <a href="//shopiumbot.com" target="_blank"></a>
                </div>
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="text-center"><?= Yii::t('app/admin', 'LOGIN_ADMIN_PANEL') ?></h5>
                    </div>     
                    <div style="padding-top:15px" class="card-body">

                        <?= $content ?>

                    </div>                       
                </div> 
                <div class="text-center copyright"><?= Yii::$app->powered() ?></div>
            </div>
        </div>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
