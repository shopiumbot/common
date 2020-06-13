<?php

namespace core\modules\pages\models;

use core\components\traits\query\QueryTrait;
use yii\db\ActiveQuery;

class PagesQuery extends ActiveQuery {

    use QueryTrait;
}
