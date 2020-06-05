<?php
use panix\ext\tinymce\TinyMce;
use core\modules\shop\models\Category;
use panix\engine\bootstrap\Alert;

/**
 * @var $form \panix\engine\bootstrap\ActiveForm
 * @var $model \core\modules\shop\models\Category
 */
if (Yii::$app->request->get('parent_id')) {
    $parent = Category::findOne(Yii::$app->request->get('parent_id'));
    echo Alert::widget([
        'options' => [
            'class' => 'alert-info',
        ],
        'body' => "Добавление в категорию: " . $parent->name,
    ]);
}
?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'icon')->textInput(['maxlength' => 5])->hint('Пример: 📂') ?>

