<?php

namespace core\modules\shop\models\query;


use yii\db\ActiveQuery;
use panix\engine\traits\query\DefaultQueryTrait;
use panix\engine\traits\query\TranslateQueryTrait;

class AttributeQuery extends ActiveQuery
{

    use DefaultQueryTrait;


    /**
     * Отобрадение атрибутов в списке
     * @return $this
     */
    public function displayOnList()
    {
        return $this->andWhere([$this->modelClass::tableName().'.display_on_list' => 1]);
    }

    /**
     * Отобрадение атрибутов в pdf печатей
     * @return $this
     */
    public function displayOnPdf()
    {
        return $this->andWhere([$this->modelClass::tableName().'.display_on_pdf' => 1]);
    }
}
