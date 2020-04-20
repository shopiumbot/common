<?php

use panix\engine\Html;
use yii\widgets\Breadcrumbs;


\core\web\themes\basic\ThemeAsset::register($this);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?= $this->render('partials/_header'); ?>


    <div class="container">
        <?php
        if (isset($this->context->breadcrumbs)) {
            echo Breadcrumbs::widget([
                'links' => $this->context->breadcrumbs,
            ]);
        }
        ?>
        <?= $content ?>

    </div>
</div>

<?= $this->render('partials/_footer'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
