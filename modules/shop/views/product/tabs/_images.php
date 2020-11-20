<?php

use panix\engine\Html;
use panix\ext\fancybox\Fancybox;
use core\modules\images\models\ImageSearch;
use panix\engine\widgets\Pjax;
use panix\engine\bootstrap\Modal;

/**
 * @var $form \panix\engine\bootstrap\ActiveForm
 */
$plan = Yii::$app->params['plan'][Yii::$app->user->planId];

?>
<?= Fancybox::widget(['target' => 'a.fancybox']); ?>
<?= $form->field($model, 'file[]')->fileInput(['multiple' => true])->hint($model::t('UPLOAD_IMAGE_HINT',[
    'formats'=>implode(', ',['jpg','jpeg','gif','png']),
    'current'=>($plan['product_upload_files'] - count($model->images)),
    'limit'=>$plan['product_upload_files']
])); ?>

<?php



?>

<?php

$script = <<< JS
$(document).on('click','.attachment-delete', function(e) {
    var id = $(this).attr('data-id');
    console.log('test');
    //return false;
    $.ajax({
       url: $(this).attr('href'),
       type:'POST',
       data: {id: id},
       dataType:'json',
       success: function(data) {
            if(data.success){
                common.notify(data.message,"success");
                $('tr[data-key="'+id+'"]').remove();
                //$.pjax.reload({container:'#pjax-grid-image'});
                common.removeLoader();
            }
       }
    });
    return false;
});
        
        
        
        $('.copper').on('click', function(e) {
      //  var id = $(this).attr('data-id');
    $.ajax({
       url: $(this).attr('href'),
       type:'POST',
      // data: {id: id},
       success: function(data) {
        $('#cropper-body').html(data);
        $('#cropper-modal').modal('show')
       }
    });
        return false;
});
JS;
$this->registerJs($script); //$position

$searchModel = new ImageSearch();
$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), ['model' => $model, 'product_id' => $model->primaryKey]);


Pjax::begin([
    'dataProvider' => $dataProvider,
]);
echo panix\engine\grid\GridView::widget([
    //'id' => 'grid-images',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'enableLayout' => false,
    //'layout'=>'{items}',
    'rowOptions' => function ($model, $index, $widget, $grid) {
        $coverClass = ($model->is_main) ? 'bg-success active sortable-column' : 'sortable-column';
        return ['class' => $coverClass];
    },
    'columns' => [
        [
            'class' => 'panix\engine\grid\sortable\Column',
            'url' => ['/admin/images/default/sortable'],
        ],
        [
            'attribute' => 'image',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function ($model) {
                return Html::a(Html::img($model->getUrl('100x100'), ['class' => 'img-thumbnail']), $model->getUrl(), ['class' => 'fancybox']);
            },
        ],
        [
            'attribute' => 'is_main',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return Html::radio('AttachmentsMainId', $model->is_main, [
                    'value' => $model->id,
                    'class' => 'check',
                    'data-toggle' => "tooltip",
                    'title' => Yii::t('app/default', 'IS_MAIN'),
                    'id' => 'main_image_' . $model->id
                ]);
            },
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{resize} {settings} {delete}',
            'filter' => false,
            'buttons' => [
                /* 'resize' => function ($url, $data, $key) {
                     return Html::a(Html::icon('resize'), ['s'], array('class' => 'btn btn-sm btn-default attachment-zoom', 'data-fancybox' => 'gallery'));
                 },
                 'settings' => function ($url, $data, $key) {
                     return Html::a(Html::icon('settings'), ['/admin/images/default/edit-crop', 'id' => $data->id], array('class' => 'btn btn-sm btn-default copper'));
                 },*/
                'delete' => function ($url, $data, $key) use ($model) {
                    return Html::a(Html::icon('delete'), ['/admin/images/default/delete', 'id' => $data->id], [
                        'class' => 'btn btn-sm btn-danger attachment-delete',
                        'data-id' => $data->id,
                        //'data-object_id' => $model->id,
                        'data-pjax'=>'0',
                        //'data-model' => get_class($model)
                    ]);
                },
            ]
        ]
    ],
    'filterModel' => $searchModel
]);
Pjax::end();
?>









