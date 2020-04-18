<?php

namespace core\modules\shop\components;

use panix\engine\Html;
use yii\base\Model;
use yii\grid\DataColumn;

/**
 *
 *
 * [
 * 'class' => 'core\modules\shop\components\EavColumns',
 * 'attribute' => 'eav_size',
 * 'header' => 'Размеры',
 * 'contentOptions' => ['class' => 'text-center']
 * ];
 *
 *
 */
class EavColumn extends DataColumn
{

    public $format = 'raw';




}
