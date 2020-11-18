<?php

namespace core\components\traits\query;

trait QueryTrait
{

    public function init2()
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (isset($modelClass::getTableSchema()->columns['ordern'])) {
            $this->addOrderBy(["{$tableName}.ordern" => SORT_DESC]);
        }
        parent::init();
    }
    /**
     * @param int $state
     * @return $this
     */
    public function published($state = 1)
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (isset($modelClass::getTableSchema()->columns['switch'])) {
            $this->andWhere(["{$tableName}.switch" => $state]);
        }
        return $this;
    }


    /**
     * @param int $sort SORT_DESC or SORT_ASC
     * @return $this
     */
    public function sort($sort = SORT_DESC)
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (isset($modelClass::getTableSchema()->columns['ordern'])) {
            $this->addOrderBy(["{$tableName}.ordern" => $sort]);
        }
        return $this;
    }
}