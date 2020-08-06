<?php

namespace core\modules\shop\models\traits;

use core\modules\shop\components\collections\CList;
use Yii;

trait EavQueryTrait
{


    public function applyAttributes(array $attributes)
    {
        if (empty($attributes))
            return $this;
        return $this->withEavAttributes($attributes);
    }

    public function withEavAttributes($attributes = [])
    {
        // If not set attributes, search models with anything attributes exists.
        //if (empty($attributes)) {
       //     $attributes = $this->getSafeAttributesArray();
       // }

        // $attributes be array of elements: $attribute => $values
        return $this->getFindByEavAttributes2($attributes);
    }


    public function getFindByEavAttributes($attributes)
    {
        $class = $this->modelClass;
        $pk = $class::tableName() . '.id';
        $i = 0;
        foreach ($attributes as $attribute => $values) {
            // If search models with attribute name with specified values.
            if (is_string($attribute)) {
                // Get attribute compare operator
                if (!is_array($values)) {
                    $values = array($values);
                }
                sort($values);


                $cache = \Yii::$app->cache->get("attribute_" . $attribute);
                //anti d-dos убирает лишние значение с запроса.
                if ($cache) {
                    $values = array_intersect($cache[$attribute], $values);
                }
                foreach ($values as $value) {
                    $this->join('JOIN', '{{%shop__product_attribute_eav}} eavb' . $i, "{$pk}=`eavb{$i}`.`entity`");
                    $this->andWhere(['IN', "`eavb$i`.`value`", $values]);
                    $i++;
                }
            } // If search models with attribute name with anything values.
            elseif (is_int($attribute)) {
                $this->join('JOIN', '{{%shop__product_attribute_eav}} eavb' . $i, "$pk=`eavb$i`.`entity` AND eavb$i.attribute = '$values'");
                $i++;
            }
        }

        //$this->distinct(true);
       $this->groupBy("{$pk}");
        // echo $this->createCommand()->getRawSql();die;
        return $this;
    }

    public function getFindByEavAttributes2($attributes)
    {
        $class = $this->modelClass;
        $pk = $class::tableName() . '.`id`';
        $i = 0;
       // echo $this->createCommand()->getRawSql();die;
        foreach ($attributes as $attribute => $values) {
            // If search models with attribute name with specified values.
            if (is_string($attribute)) {
                // Get attribute compare operator
                if (!is_array($values)) {
                    $values = [$values];
                }
                $values = array_unique($values);
                sort($values);

                $cache = Yii::$app->cache->get("attribute_" . $attribute);
                //anti d-dos убирает лишние значение с запроса.
                if ($cache) {
                    $values = array_intersect($cache[$attribute], $values);
                }

                $this->join('JOIN', '{{%shop__product_attribute_eav}} eavb' . $i, "$pk=`eavb$i`.`entity`");
                $this->andWhere(['IN', "`eavb$i`.`value`", $values]);
                $i++;

            } // If search models with attribute name with anything values.
            elseif (is_int($attribute)) {
                $this->join('JOIN', '{{%shop__product_attribute_eav}} eavb' . $i, "$pk=`eavb$i`.`entity` AND eavb$i.attribute = '$values'");
                $i++;
            }
        }



        $this->distinct(true);

        $this->groupBy("{$pk}");
        //$this->addGroupBy("{$pk}");
      //echo $this->createCommand()->getRawSql();die;
        return $this;
    }

}
