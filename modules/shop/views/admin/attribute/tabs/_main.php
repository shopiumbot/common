<?php

use yii\helpers\ArrayHelper;
use core\modules\shop\models\AttributeGroup;
use core\modules\shop\models\Attribute;
use panix\engine\Html;

/**
 * @var $this \yii\web\View
 * @var $form \panix\engine\bootstrap\ActiveForm
 * @var $model \core\modules\shop\models\Attribute
 */
?>
<?= $form->field($model, 'title')->textInput(['maxlength' => 255]); ?>

<?= $form->field($model, 'abbreviation')->textInput(['maxlength' => 255]); ?>
<?= $form->field($model, 'required')->checkbox(); ?>
<?php if ($model->type == Attribute::TYPE_COLOR) {
    echo Html::activeHiddenInput($model,'type');
} else {
    echo $form->field($model, 'type')->dropDownList(Attribute::typesList());
}
?>
<?= $form->field($model, 'use_in_variants')->dropDownList([1 => Yii::t('app/default', 'YES'), 0 => Yii::t('app/default', 'NO')]); ?>
<?= $form->field($model, 'select_many')->dropDownList([1 => Yii::t('app/default', 'YES'), 0 => Yii::t('app/default', 'NO')]); ?>
<?= $form->field($model, 'sort')->dropDownList(Attribute::sortList(), ['prompt' => $model::t('SORT_DEFAULT')]); ?>




