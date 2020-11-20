<?php

use yii\helpers\ArrayHelper;

/**
 * @var $this \yii\web\View
 * @var $form \yii\widgets\ActiveForm
 * @var $model \core\modules\shop\models\Currency
 */
?>


<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?php // $form->field($model, 'iso')->textInput(['maxlength' => 10]) ?>
<?= $form->field($model, 'symbol')->textInput(['maxlength' => 10]) ?>
<?= $form->field($model, 'rate')->textInput() ?>
<?= $form->field($model, 'is_main')->checkbox() ?>
<?php echo $form->field($model, 'is_default')->checkbox() ?>
<?php /* $form->field($model, 'is_default')->dropDownList(ArrayHelper::map($model::currenciesList(), 'iso', function ($data) {
    return html_entity_decode($data['iso'] . ' &mdash; '.$data['name']);
}))*/ ?>


