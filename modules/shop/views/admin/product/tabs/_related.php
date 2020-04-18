<?php

use panix\engine\Html;
use yii\widgets\Pjax;

\core\modules\shop\bundles\AdminAsset::register($this);
?>

<?php
//\yii\helpers\VarDumper::dump($model,10,true);
//echo $model->getRelatedProductCount(); 
?>

<table class="table table-striped table-bordered" id="relatedProductsTable">
    <?php
    //print_r($model->relatedProducts2);
    ?>
    <?php



    foreach ($model->relatedProducts as $related) { ?>
        <tr>
            <input type="hidden" value="<?php echo $related->id ?>" name="RelatedProductId[]">
            <td class="image text-center relatedProductLine<?= $related->id ?>"><?= $related->renderGridImage('50x50'); ?></td>
            <td>
                <?= Html::a($related->name, ['/admin/shop/products/update', 'id' => $related->id], [
                    'target' => '_blank'
                ]);
                ?>
            </td>
            <td class="text-center">
                <a class="btn btn-danger" href="#" onclick="$(this).parents('tr').remove();"><?= Yii::t('app/default', 'DELETE') ?></a>
            </td>
        </tr>
    <?php } ?>

</table>


<br/><br/>


<?php
/* Pjax::begin([
  'id' => 'pjax-container-related', 'enablePushState' => false,
  ]); */
?>


<?php


$searchModel = new core\modules\shop\models\search\ProductSearch();
$searchModel->exclude[] = $exclude;

foreach ($model->relatedProducts as $d) {
    //  $searchModel->exclude[] = $d->id;
}

$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


echo \panix\engine\grid\GridView::widget([
    'id' => 'RelatedProductsGrid',
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
                        'onClick' => 'return AddRelatedProduct(this);',
                        'data-pjax' => false
                    ]);
                },
            ],
        ]
    ]
]);
?>
<?php //Pjax::end(); ?>


