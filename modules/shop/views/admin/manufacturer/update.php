<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;


?>


<?php
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]);
?>
    <div class="card">
        <div class="card-header">
            <h5><?= Html::encode($this->context->pageName) ?></h5>
        </div>
        <div class="card-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]); ?>
        </div>
        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>