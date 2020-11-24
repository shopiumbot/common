<?php

namespace core\modules\menu\models;

use core\components\traits\query\QueryTrait;
use panix\engine\traits\query\TranslateQueryTrait;
use yii\db\ActiveQuery;

class MenuQuery extends ActiveQuery {

    use QueryTrait, TranslateQueryTrait;
}
