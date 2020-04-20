<?php

namespace core\modules\shop\models\query;


use yii\db\ActiveQuery;
use panix\engine\traits\query\DefaultQueryTrait;
use panix\engine\traits\query\TranslateQueryTrait;

class AttributeQuery extends ActiveQuery
{

    use DefaultQueryTrait;
}
