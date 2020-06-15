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
        $model->bot_admins = explode(',', $model->bot_admins);
        $form = ActiveForm::begin([
            'options' => [],
            'fieldConfig' => [
                'template' => "<div class=\"col-sm-5 col-md-5 col-lg-4 col-xl-3\">{label}</div>\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-form-label',
                    'offset' => 'offset-sm-5 offset-lg-4 offset-xl-3',
                    'wrapper' => 'col-sm-7 col-md-7 col-lg-8 col-xl-9',
                    'error' => '',
                    'hint' => '',
                ],
            ]
        ]);
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
                <?php /*echo $form->field($model, 'bot_admins')
                    ->widget(\panix\ext\taginput\TagInput::class, ['placeholder' => 'ID'])
                    ->hint('Введите ID и нажмите Enter');*/
                ?>

                <?= $form->field($model, 'bot_admins')
                    ->widget(\panix\ext\bootstrapselect\BootstrapSelect::class, [
                        'items' => \shopium\mod\telegram\models\User::dropdown(),
                        'options' => ['multiple' => true]
                    ]);
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


        <?php
        $formCommand = ActiveForm::begin([
            'options' => [],
            'fieldConfig' => [
                'template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-form-label',
                    'offset' => '',
                    'wrapper' => 'col-sm-12 col-md-12 col-lg-12 col-xl-12',
                    'error' => '',
                    'hint' => '',
                ],
            ]
        ]);
        ?>
        <div class="card">
            <div class="card-header">
                <h5>Команды бота</h5>
            </div>
            <div class="card-body p-0">
                <?php

                $command = new \shopium\mod\telegram\models\forms\CommandsForm();


                if ($command->load(Yii::$app->request->post())) {
                    if ($command->validate()) {
                        $command->save();
                        Yii::$app->session->setFlash("success", Yii::t('app/default', 'Команды бота успешно сохранены'));
                        return Yii::$app->response->refresh();
                    } else {
                        // print_r($model->errors);die;
                    }

                }


                echo $formCommand->field($command, 'data')->widget(\panix\ext\multipleinput\MultipleInput::class, [
                    'max' => 10,
                    'min' => 1,
                    'allowEmptyList' => false,
                    //'enableGuessTitle' => true,
                    'sortable' => true,
                    'addButtonPosition' => \panix\ext\multipleinput\MultipleInput::POS_ROW, // show add button in the header
                    'columns' => [
                        [
                            'name' => 'command',
                            'enableError' => false,
                            'title' => 'Команда',
                            'options' => [
                                'placeholder' => 'Например: /start',
                            ],
                            'headerOptions' => [
                                'style' => 'width: 250px;',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'enableError' => false,
                            'title' => 'Описание',
                            'options' => [
                                'placeholder' => 'Например: Старт',
                            ],
                        ],
                    ]
                ])->label(false);
                ?>

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
