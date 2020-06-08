<?php

namespace core\modules\telegram\models\query;


use yii\db\ActiveQuery;
use panix\engine\traits\query\DefaultQueryTrait;

class MessageQuery extends ActiveQuery
{

    use DefaultQueryTrait;
}
