<?php

use panix\engine\Html;
use panix\engine\widgets\Pjax;
use core\modules\shop\models\AttributeOption;

\core\modules\shop\bundles\admin\AttributeAsset::register($this);
?>

<style type="text/css">
    table.optionsEditTable input[type="text"] {
        width: 200px;
    }

    tr.copyMe {
        display: none;
    }

</style>

<table>
    <tr class="copyMe">
        <td class="text-center">&mdash;</td>

            <td>
                <input name="sample" type="text" class="value form-control" />
            </td>

        <td class="text-center">&mdash;</td>
        <td class="text-center">
            <a href="#" class="delete-option-attribute btn btn-sm btn-default"><i class="icon-delete"></i></a>
        </td>
    </tr>
</table>
<?php


$columns = [];
$columns[] = [
    'class' => 'panix\engine\grid\sortable\Column',
    'url' => ['/admin/shop/attribute/sortableOptions']
];
$data = [];
$data2 = [];
$test = [];
foreach ($model->options as $k => $o) {
    //echo print_r($o->translations);
    $data2['delete'] = '<a href="#" class="delete-option-attribute btn btn-sm btn-outline-danger"><i class="icon-delete"></i></a>';
   // foreach (Yii::$app->languageManager->languages as $k => $l) {



    $data2['name'] = Html::textInput('options[' . $o->id . '][]', Html::decode($o->value), ['class' => 'form-control']);

        $data2['products'] = Html::a($o->productsCount, ['/admin/shop/product/index', 'ProductSearch[eav][' . $model->name . ']' => $o->id], ['target' => '_blank']);
        $data[$o->id] = (array)$data2;

}



$columns[] = [
    'header' => 'sdadsa',
    'attribute' => 'name',
    'format' => 'raw',
    //  'value' => '$data->name'
];

    $sortAttributes[] = 'name';

$columns[] = [
    'header' => Yii::t('shop/admin', 'PRODUCT_COUNT'),
    'attribute' => 'products',
    'format' => 'raw',
    'contentOptions' => ['class' => 'text-center'],
];
$columns[] = [
    'header' => Yii::t('app/default', 'OPTIONS'),
    'attribute' => 'delete',
    'format' => 'html',
    'contentOptions' => ['class' => 'text-center'],
    'filterOptions' => ['class' => 'text-center'],
    'filter' => Html::a(Html::icon('add'), '#', ['title' => 'Добавить опцию', 'class' => 'btn btn-sm btn-success', 'id' => 'add-option-attribute'])
];


$data_array = new \yii\data\ArrayDataProvider([
    'allModels' => $data,
    'pagination' => false,
]);



echo panix\engine\grid\GridView::widget([
    'tableOptions' => ['class' => 'table table-striped optionsEditTable'],
    'dataProvider' => $data_array,
    'rowOptions' => ['class' => 'sortable-column'],
    'enableLayout' => false,
    'layout' => '{items}',
    'columns' => $columns,
    'filterModel' => true
]);

?>
