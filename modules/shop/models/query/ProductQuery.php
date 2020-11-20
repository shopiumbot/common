<?php

namespace core\modules\shop\models\query;

use core\components\traits\query\QueryTrait;
use panix\engine\traits\query\TranslateQueryTrait;
use yii\db\ActiveQuery;
use core\modules\shop\models\traits\EavQueryTrait;
use core\modules\shop\models\Category;
use core\modules\shop\models\Product;
use core\modules\shop\models\ProductCategoryRef;

class ProductQuery extends ActiveQuery
{

    use QueryTrait, EavQueryTrait, FilterQueryTrait, TranslateQueryTrait;

    /**
     * Product by category
     *
     * @return $this
     */
    public function category()
    {
        $this->joinWith(['category']);
        return $this;
    }


    /**
     * @param $manufacturers array|int
     * @return $this
     */
    public function applyManufacturers($manufacturers)
    {
        if (!is_array($manufacturers))
            $manufacturers = [$manufacturers];

        if (empty($manufacturers))
            return $this;

        sort($manufacturers);

        $this->andWhere(['manufacturer_id' => $manufacturers]);
        return $this;
    }

    /**
     * @param $categories array|int|object
     * @return $this
     */
    public function applyCategories($categories)
    {
        if ($categories instanceof Category)
            $categories = [$categories->id];
        else {
            if (!is_array($categories))
                $categories = [$categories];
        }
        //  $tableName = ($this->modelClass)->tableName();
        $this->leftJoin(ProductCategoryRef::tableName(), ProductCategoryRef::tableName() . '.`product`=' . $this->modelClass::tableName() . '.`id`');
        $this->andWhere([ProductCategoryRef::tableName() . '.`category`' => $categories]);

        return $this;
    }


    /**
     * Product by manufacturer
     *
     * @return $this
     */
    public function manufacturer()
    {
        $this->joinWith(['manufacturer']);
        return $this;
    }


    /**
     * @param null $q
     * @return $this
     */
    public function applySearch($q = null)
    {
        if ($q) {
            $this->andWhere(['LIKE', Product::tableName() . '.sku', $q]);
            $this->orWhere(['LIKE', 'translate.name', $q]);
        }
        return $this;
    }

    public function new($start, $end)
    {
        /** @var Product $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->between($start, $end, 'created_at');
        return $this;
    }


    /**
     * @param integer $current_id
     * @param array $wheres
     * @return $this
     */
    public function next($current_id, $wheres = [])
    {
        /** @var Product $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();

        $subQuery = (new \yii\db\Query())->select('MIN(`id`)')
            ->from($tableName . ' next')
            ->where(['>', 'next.id', $current_id]);

        if ($wheres) {
            $subQuery->andWhere($wheres);
        }

        $this->where(['=', 'id', $subQuery]);

        return $this;
    }

    /**
     * @param integer $current_id
     * @param array $wheres
     * @return $this
     */
    public function prev($current_id, $wheres = [])
    {
        /** @var Product $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();

        $subQuery = (new \yii\db\Query())->select('MAX(`id`)')
            ->from($tableName . ' prev')
            ->where(['<', 'prev.id', $current_id]);

        if ($wheres) {
            $subQuery->andWhere($wheres);
        }

        $this->where(['=', 'id', $subQuery]);

        return $this;
    }


    public function int2between($start, $end, $attribute = 'created_at')
    {
        /** @var Product $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['<=', $tableName . '.' . $attribute, $start]);
        $this->andWhere(['>=', $tableName . '.' . $attribute, $end]);
        return $this;
    }

    /**
     * @param string $attribute
     * @return $this
     */
    public function isNotEmpty($attribute)
    {
        /** @var Product $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['IS NOT', $tableName . '.' . $attribute, null]);
        $this->andWhere(['!=', $tableName . '.' . $attribute, '']);
        return $this;
    }


    /**
     * @return $this
     */
    public function isNotAvailability()
    {
        /** @var Product $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        //$this->andWhere(['IS NOT', $tableName . '.availability', null]);
        $this->andWhere(['!=', $tableName . '.availability', Product::AVAILABILITY_NOT]);
        return $this;
    }
}
