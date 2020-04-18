<?php

echo \yii\widgets\ListView::widget([
    //'id'=>'list-product',
    'dataProvider' => $provider,
    'itemView' => $itemView,
    //'layout' => '{sorter}{summary}{items}{pager}',
    'layout' => '{items}{pager}',
    'emptyText' => 'Empty',
    'options' => ['class' => 'row list-view '.$itemView],
    'itemOptions' => ['class' => 'item'],
    'sorter' => [
        //'class' => \yii\widgets\LinkSorter::class,
        'attributes'=>['price','sku']
    ],
    'pager' => [
        'class' => \panix\wgt\scrollpager\ScrollPager::class,
        'triggerTemplate' => '<div class="ias-trigger" style="text-align: center; cursor: pointer;width: 100%;">{text}</div>',
        'spinnerTemplate' => '<div class="ias-spinner" style="text-align: center;width: 100%;"><img src="{src}" alt="" /></div>',
        'spinnerSrc'=>$this->context->assetUrl.'/images/ajax.gif'

    ],
    'emptyTextOptions' => ['class' => 'alert alert-info']
]);
?>