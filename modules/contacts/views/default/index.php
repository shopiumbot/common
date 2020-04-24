<?php

use panix\engine\CMS;
use panix\engine\Html;
use yii\widgets\ActiveForm;
use core\modules\contacts\models\SettingsForm;

$config = Yii::$app->settings->get('contacts');

echo date('N');
?>

<div class="row">
    <div class="col-sm-7">
        <?php
        echo core\modules\contacts\widgets\map\MapWidget::widget(['map_id' => 1]);
        ?>
    </div>
    <div class="col-sm-5">

        <?php if (Yii::$app->session->hasFlash('success')) { ?>
            <div class="alert alert-success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php } ?>
        <?php if (isset($config->address) && isset($config->address[Yii::$app->language])) { ?>
            <h4><?= Yii::t('contacts/default', 'ADDRESS'); ?></h4>
            <div class="mb-1 pl-md-3 icon-location"><?= $config->address[Yii::$app->language]; ?></div>
        <?php } ?>
        <?php if (isset($config->schedule)) { ?>
            <h4 class="mt-4"><?= Yii::t('contacts/default', 'SCHEDULE'); ?></h4>
            <?php foreach ($config->schedule as $key => $schedule) { ?>
                <div class="mb-1 pl-md-3">
                    <strong><?= SettingsForm::dayList()[$key]; ?>.</strong>

                    <?php if (!empty($schedule['start_time']) || !empty($schedule['end_time'])) { ?>

                        с <?= $schedule['start_time']; ?> до <?= $schedule['end_time']; ?>
                    <?php } else { ?>
                        <?= SettingsForm::t('DAY_OFF'); ?>
                    <?php } ?>

                    <?php if (date('N') == $key + 1) { ?>
                        <?php if (Yii::$app->getModule('contacts')->getTodayOpen($key)) { ?>
                            <span class="font-italic text-danger">(<?= Yii::t('contacts/default', 'IS_CLOSE'); ?>)</span>
                        <?php } else { ?>
                            <span class="font-italic text-success">(<?= Yii::t('contacts/default', 'IS_OPEN'); ?>)</span>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if (isset($config->email)) { ?>
            <h4 class="mt-4"><?= Yii::t('contacts/default', 'EMAIL'); ?></h4>
            <?php foreach (explode(',', $config->email) as $email) { ?>
                <div class="mb-1 pl-md-3"><?= Html::mailto($email); ?></div>
            <?php } ?>
        <?php } ?>
        <?php if (isset($config->phone)) { ?>
            <h4 class="mt-4"><?= Yii::t('contacts/default', 'PHONES'); ?></h4>
            <?php foreach ($config->phone as $phone) { ?>
                <div class="mb-1 pl-md-3">
                    <?= Html::tel($phone['number'], ['class' => 'phone h5 ' . CMS::slug(CMS::phoneOperator($phone['number']))]); ?> <?= $phone['name']; ?>
                </div>
            <?php } ?>
        <?php } ?>

    </div>
    <div class="line-title"></div>
    <div class="col-sm-6 offset-md-3">


        <div class="text-center mt-4"><h2><?= Yii::t('contacts/default', 'FORM_TITLE'); ?></h2></div>


        <?php if (Yii::$app->session->hasFlash('success')) { ?>
            <div class="alert alert-success"><?= Yii::$app->session->getFlash('success'); ?></div>
        <?php } ?>


        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
        <?php if (Yii::$app->user->isGuest) { ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'email') ?>
        <?php } ?>

        <?php
        if (!Yii::$app->user->phone) {
            echo $form->field($model, 'phone')->widget(\panix\ext\telinput\PhoneInput::class)->label($model->getAttributeLabel('phone'),['class'=>'']);
        }
        ?>
        <?= $form->field($model, 'text')->textArea(['rows' => 6])->label($model->getAttributeLabel('text'),['class'=>'']) ?>
        <?php if (Yii::$app->settings->get('app', 'captcha_class') && $config->feedback_captcha && Yii::$app->user->isGuest) { ?>
            <?php
            /*echo $form->field($model, 'verifyCode')->widget(yii\captcha\Captcha::class, [
                'captchaAction' => 'default/captcha',
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ])*/
            ?>


            <?php echo $form->field($model, 'verifyCode')->widget(Yii::$app->settings->get('app', 'captcha_class')); ?>
        <?php } ?>

        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('app/default', 'SEND'), ['class' => 'btn btn-warning', 'name' => 'contact-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>


<?php
/* $coords = [];
  $coords[] = new LatLng(['lat' => 46.468252, 'lng' => 30.740576]);
  $coords[] = new LatLng(['lat' => 46.453163, 'lng' => 30.751179]);

  $coord = new LatLng(['lat' => 46.458252, 'lng' => 30.742576]);
  $map = new Map([
  'center' => $coord,
  'zoom' => 14,
  ]);
  $markers = [];
  foreach ($coords as $coord) {

  $markers = new Marker([
  'position' => $coord,
  'title' => 'My Home Town',
  ]);

  $markers->attachInfoWindow(
  new InfoWindow([
  'content' => '<p>This is my super cool content</p>'
  ])
  );

  $map->addOverlay($markers);
  }

  echo $map->display(); */
?>
