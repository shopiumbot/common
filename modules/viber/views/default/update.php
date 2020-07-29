<?php
use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;


$form = ActiveForm::begin();
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">

        <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        <?=
        $form->field($model, 'text')->widget(\shopium\mod\telegram\widgets\editor\EditorInput::class,[

            ]);
        ?>

    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
