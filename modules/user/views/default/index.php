<?php

use panix\engine\Html;
use panix\engine\CMS;
use panix\engine\bootstrap\ActiveForm;
use panix\engine\helpers\TimeZoneHelper;


/**
 * @var yii\web\View $this
 * @var core\modules\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */

?>


<?php if ($flash = Yii::$app->session->getFlash("success")) { ?>
    <div class="alert alert-success">
        <?= $flash ?>
    </div>
<?php } ?>


<?php if (!$model->status && !$model->isNewRecord) { ?>
    <div class="alert alert-warning">
        Аккаунет не
        актевирован. <?= Html::a('отправить владельцу письмо с инструкций?', ['send-active', 'id' => $user->id]); ?>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-7 col-lg-6 col-xl-7">
        <?php

        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        ?>
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <div class="card-body">

                <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'token')->textInput(['maxlength' => 255]) ?>

                <?= $form->field($model, 'phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>
                <?= $form->field($model, 'gender')->dropDownList([0 => $model::t('FEMALE'), 1 => $model::t('MALE')], ['prompt' => 'Не указано']); ?>
                <?= $form->field($model, 'timezone')->dropDownList(TimeZoneHelper::getTimeZoneData(), ['prompt' => 'Не указано']); ?>
                <?= $form->field($model, 'bot_admins')
                    ->widget(\panix\ext\taginput\TagInput::class, ['placeholder' => 'ID'])
                    ->hint('Введите ID и нажмите Enter');
                ?>

                <?php if ($model->isNewRecord) { ?>
                    <?= $form->field($model, 'new_password')->passwordInput(); ?>
                    <?= $form->field($model, 'password_confirm')->passwordInput(); ?>
                <?php } ?>


            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton(Yii::t('app/default', $model->isNewRecord ? 'CREATE' : 'SAVE'), ['class' => 'btn btn-success']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-5 col-lg-6 col-xl-5">


        <?php $form = ActiveForm::begin([
            'id' => 'reset-form',
            'fieldConfig' => [
                'template' => "<div class=\"col-sm-6 col-lg-6\">{label}</div>\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-form-label',
                    'offset' => 'offset-sm-6 offset-lg-6',
                    'wrapper' => 'col-sm-6 col-lg-6',
                    'error' => '',
                    'hint' => '',
                ],
                // 'labelOptions' => ['class' => 'col-lg-22 control-label'],
            ],
        ]); ?>


        <div class="card">
            <div class="card-header">
                <?= Yii::t('user/default', 'CHANGE_PASSWORD'); ?>
            </div>
            <div class="card-body">
                <?= $form->field($changePasswordForm, 'current_password')->passwordInput() ?>
                <?= $form->field($changePasswordForm, 'new_password')->passwordInput() ?>
                <?= $form->field($changePasswordForm, 'password_confirm')->passwordInput() ?>

            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton(Yii::t('app/default', 'UPDATE'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
