<?php

use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;

Pjax::begin([
    'dataProvider' => $dataProvider,
]);
echo GridView::widget([
    'id' => 'grid-attribute',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName],
    'showFooter' => true,
    'enableColumns' => false,
    'columns' => [
        ['class' => 'panix\engine\grid\sortable\Column'],
        ['class' => 'panix\engine\grid\columns\CheckboxColumn'],
        [
            'header' => 'title',
            'attribute' => 'title',
            'format' => 'raw',
        ],
        ['class' => 'panix\engine\grid\columns\ActionColumn'],
    ]
]);
Pjax::end();

