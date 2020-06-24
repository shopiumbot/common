<?php
use panix\engine\grid\GridView;
use panix\engine\widgets\Pjax;
use panix\ext\fancybox\Fancybox;
use core\modules\shop\bundles\admin\ProductIndex;

echo Fancybox::widget(['target' => '.image a']);
$buttons = [];
if ($this->context->created) {
    $buttons[] =
        [
            'url' => ['create'],
            'label' => Yii::t('shop/admin', 'CREATE_PRODUCT'),
            'icon' => 'add',
        ];
}
Pjax::begin(['dataProvider' => $dataProvider]);
ProductIndex::register($this);
echo GridView::widget([
    'id' => 'grid-product',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => [
        'title' => $this->context->pageName,
        'buttons' => $buttons
    ],
    'showFooter' => true,
    //'footerRowOptions' => ['class' => 'text-center'],
]);
Pjax::end();


