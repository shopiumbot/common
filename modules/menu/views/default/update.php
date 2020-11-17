<?php
use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use core\modules\pages\models\Pages;

/**
 * @var $this \yii\web\View
 * @var $model \core\modules\menu\models\Menu
 */


$form = ActiveForm::begin();
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">

        <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

        <?php
        if (!in_array($model->id, $model->disallow_delete))
            echo $form->field($model, 'content')->widget(\core\components\TinyMceTelegram::class);
        ?>

    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
