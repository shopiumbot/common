<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;

?>


<?php //echo $this->render('_search', ['model' => $searchModel]);   ?>


<?php Pjax::begin(); ?>
<?=

GridView::widget([
  //  'id'=>'grid-attribute-group',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName],
    'columns' => [
        [
            'class' => 'panix\engine\grid\sortable\Column',
        ],
        'name',
        ['class' => 'core\components\ActionColumn',
            'template' => '{switch}{update}{delete}',
        ],
    ],
]);
?>
<?php Pjax::end(); ?>

