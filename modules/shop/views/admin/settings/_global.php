<?php
/**
 * @var \panix\engine\bootstrap\ActiveForm $form
 * @var \core\modules\shop\models\forms\SettingsForm $model
 */
?>

<?php echo $form->field($model, 'per_page') ?>
<?php echo $form->field($model, 'group_attribute')->checkbox(); ?>
<?php echo $form->field($model, 'smart_bc')->checkbox(); ?>
<?php echo $form->field($model, 'smart_title')->checkbox(); ?>
<?php echo $form->field($model, 'label_expire_new')->dropDownList($model::labelExpireNew(),['prompt'=>Yii::t('app/default','OFF')]); ?>
