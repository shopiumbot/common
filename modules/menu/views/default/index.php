<?php

use yii\helpers\Html;
use panix\engine\grid\GridView;
use panix\engine\widgets\Pjax;


Pjax::begin([
    'dataProvider' => $dataProvider,
]);

echo GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName],
    'showFooter' => true,
    //   'footerRowOptions' => ['class' => 'text-center'],
    'rowOptions' => ['class' => 'sortable-column'],
    'columns' => [
        [
            'attribute' => 'name',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                \panix\engine\emoji\EmojiAsset::register(Yii::$app->controller->view);
                return \panix\engine\emoji\Emoji::emoji_unified_to_html($model->name);
            }
        ],
        [
            'class' => 'core\components\ActionColumn',
        ],

    ]
]);

Pjax::end();
