<?php
use panix\ext\tinymce\TinyMce;

/**
 * @var panix\engine\bootstrap\ActiveForm $form
 * @var \core\modules\shop\models\Manufacturer $model
 */
?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 255]); ?>
