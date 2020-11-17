<?php

namespace core\modules\menu\models;

use core\components\traits\query\QueryTrait;
use yii\db\ActiveQuery;

class MenuQuery extends ActiveQuery {

    use QueryTrait;
}
