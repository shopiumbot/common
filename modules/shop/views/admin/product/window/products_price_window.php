<?php

use panix\engine\bootstrap\ActiveForm;
use panix\engine\bootstrap\Alert;

//\core\web\themes\dashboard\AdminAsset::register($this);
\panix\engine\widgets\PjaxAsset::register($this);
\panix\engine\assets\CommonAsset::register($this);
$form = ActiveForm::begin();
?>

<?php
echo Alert::widget([
    'options' => [
        'class' => 'alert-info',
    ],
    'body' => 'Внимание товары которые привязаны к валюте и/или используют конфигурации изменены не будут',
]);
echo $form->field($model, 'price')->textInput();

?>

<?php ActiveForm::end(); ?>