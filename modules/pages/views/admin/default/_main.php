<?php
use panix\ext\tinymce\TinyMce;

/**
 * @var \panix\engine\bootstrap\ActiveForm $form
 * @var \core\modules\pages\models\Pages $model
 */
?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => 255]) ?>
<?=
$form->field($model, 'text')->widget(TinyMce::class, [
    'options' => ['rows' => 6],
]);
?>