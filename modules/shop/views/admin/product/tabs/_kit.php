<?php

use panix\engine\Html;
use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;

\core\modules\shop\bundles\AdminAsset::register($this);
?>

<?php
//\yii\helpers\VarDumper::dump($model,10,true);
//echo $model->getRelatedProductCount(); 
?>
zzzz
<table class="table table-striped" id="kitProductsTable">
    <?php
    //print_r($model->relatedProducts2);
    ?>
    <?php



    foreach ($model->kitProducts as $data) { ?>
        <tr>
            <input type="hidden" value="<?= $data->id ?>" name="kitProductId[]">
            <td class="image text-center kitProductLine<?= $data->id ?>"><?= $data->renderGridImage('50x50'); ?></td>
            <td>
                <?= Html::a($data->name, ['/admin/shop/product/update', 'id' => $data->id], [
                    'target' => '_blank'
                ]);
                ?>
            </td>
            <td class="text-center">
                <?= Html::textInput('kit[price]',$data->price,['class'=>'form-control']); ?>
            </td>
            <td class="text-center">
                <a class="btn btn-sm btn-danger" href="#" onclick="$(this).parents('tr').remove();"><?= Yii::t('app/default', 'DELETE') ?></a>
            </td>
        </tr>
    <?php } ?>

</table>


<br/><br/>



<?php


$searchModel = new core\modules\shop\models\search\ProductSearch();
$searchModel->exclude[] = $exclude;

foreach ($model->kit as $d) {
    //  $searchModel->exclude[] = $d->id;
}

$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

Pjax::begin([
    'id' => 'pjax-grid-product-kit',
]);
echo GridView::widget([
    'id' => 'grid-product-kit',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'enableLayout'=>false,
    /*'rowOptions' => function ($model, $key, $index, $grid) {
        return ['id' => $model['id']];
    },*/
    'columns' => [
        [
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function ($model) {
                return $model->renderGridImage('50x50');
            },
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            //'contentOptions' =>function ($model, $key, $index, $column){
            //              return ['class' => 'name','data-id'=>$model->id];
            //},
            'value' => function ($model, $key, $index) {
                return Html::a($model->name, ["update", "id" => $model->id], ["target" => "_blank", "class" => "product-name", "data-id" => $model->id]);
            }

            //   'value' => 'Html::link(Html::encode($data->name), array("update", "id"=>$data->id), array("target"=>"_blank","class"=>"product-name","data-id"=>$data->id))',
            // 'filter' => Html::textField('RelatedProducts[name]', $model->name)
        ],
        [
            'attribute' => 'price',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return Yii::$app->currency->number_format($model->price) . ' ' . Yii::$app->currency->main['symbol'];
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{add}',
            'buttons' => [
                'add' => function ($url, $model) { //$model->id . '/' . Html::encode($model->name)
                    return Html::a(Html::icon('add'), '#', [
                        'title' => Yii::t('app/default', 'ADD'),
                        'class' => 'btn btn-sm btn-success',
                        'onClick' => 'return addKitProduct(this);',
                        'data-pjax' => false
                    ]);
                },
            ],
        ]
    ]
]);
Pjax::end();



