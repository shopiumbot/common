<?php

namespace core\modules\shop\behaviors\nestedsets;

use yii\db\ActiveQuery;

class NestedSetsQuery extends ActiveQuery {

    public function behaviors() {
        return [
            [
                'class' => NestedSetsQueryBehavior::class,
            ]
        ];
    }

}
