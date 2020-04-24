<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;

$form = ActiveForm::begin();
?>
    <div class="card">
        <div class="card-header">
            <h5><?= $this->context->pageName ?></h5>
        </div>
        <div class="card-body">
            <?php
            echo yii\bootstrap4\Tabs::widget([
                'items' => [
                    [
                        'label' => $model::t('TAB_GENERAL'),
                        'content' => $this->render('_main', ['form' => $form, 'model' => $model]),
                        'active' => true,
                    ],
                    [
                        'label' => $model::t('TAB_SCHEDULE'),
                        'content' => $this->render('_schedule', ['form' => $form, 'model' => $model]),
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="card-footer text-center">
            <?= Html::submitButton(Yii::t('app/default', 'SAVE'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>