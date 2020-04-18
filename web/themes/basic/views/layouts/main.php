<?php

use panix\engine\Html;
use yii\widgets\Breadcrumbs;


\core\web\themes\basic\ThemeAsset::register($this);

/*$c = Yii::$app->settings->get('shop');


$this->registerJs("
        var price_penny = " . $c->price_penny . ";
        var price_thousand = " . $c->price_thousand . ";
        var price_decimal = " . $c->price_decimal . ";
     ", yii\web\View::POS_HEAD, 'numberformat');*/

//add
//Yii::$app->authManager->assign(Yii::$app->authManager->createRole('Manager'),2);

//remove
//Yii::$app->authManager->revoke(Yii::$app->authManager->createRole('Manager'),2);


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

    <?php

    /* NavBar::begin([
      'brandLabel' => 'CORNER CMS',
      'brandUrl' => Yii::$app->homeUrl,
      'options' => [
      'class' => 'navbar-inverse navbar-fixed-top',
      ],
      ]);
      echo Nav::widget([
      'options' => ['class' => 'navbar-nav navbar-right'],
      'items' => [
      ['label' => Yii::t('app','HOME'), 'url' => ['/site/index']],
      ['label' => 'About', 'url' => ['/site/about']],
      ['label' => 'Contact', 'url' => ['/site/contact']],
      ['label' => 'User', 'url' => ['/user']],
      Yii::$app->user->isGuest ?
      ['label' => 'Login', 'url' => ['/user/login']] :
      ['label' => 'Logout (' . Yii::$app->user->displayName . ')',
      'url' => ['/user/logout'],
      'linkOptions' => ['data-method' => 'post']],
      ],
      ]);


      NavBar::end(); */
    ?>

    <div class="container">
        <?php
        if (isset($this->context->breadcrumbs)) {
            echo Breadcrumbs::widget([
                'links' => $this->context->breadcrumbs,
            ]);
        }
        ?>
        <?= $content ?>

        <?php echo \panix\mod\shop\widgets\brands\BrandsWidget::widget([]); ?>
    </div>
</div>
<?= $this->render('partials/_subscribe'); ?>
<?= $this->render('partials/_footer'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
