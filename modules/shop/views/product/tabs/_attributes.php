<?php

echo app\modules\shop\components\AttributesRender::widget([
    'model' => $model,
    'view' => (Yii::$app->settings->get('shop', 'group_attribute')) ? '_attributes_group' : '_attributes_list',
    'htmlOptions' => [
        'class' => 'attributes'
    ],
]);
