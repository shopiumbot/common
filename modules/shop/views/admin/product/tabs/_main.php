<?php

use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use core\modules\shop\models\Manufacturer;
use core\modules\shop\models\Category;

/**
 * @var panix\engine\bootstrap\ActiveForm $form
 */

?>



    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

<?= $form->field($model, 'sku')->textInput(['maxlength' => 255]) ?>


<?php
echo $this->render('_prices', ['model' => $model, 'form' => $form]);
?>
<?php
/*echo $form->field($model, 'label')->dropDownList($model::labelsList(), [
    'prompt' => html_entity_decode($model::t('SELECT_LABEL'))
]);*/
?>
<?=

$form->field($model, 'manufacturer_id')->dropDownList(ArrayHelper::map(Manufacturer::find()->cache(3200, new DbDependency(['sql' => 'SELECT MAX(`updated_at`) FROM ' . Manufacturer::tableName()]))->all(), 'id', 'name'), [
    'prompt' => html_entity_decode($model::t('SELECT_MANUFACTURER_ID'))
]);
?>

<?=

$form->field($model, 'main_category_id')->dropDownList(Category::flatTree(), [
    'prompt' => html_entity_decode($model::t('SELECT_MAIN_CATEGORY_ID'))
]);
?>
<?=

$form->field($model, 'description')->textarea();

?>