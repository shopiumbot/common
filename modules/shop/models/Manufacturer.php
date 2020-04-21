<?php

namespace core\modules\shop\models;

use panix\engine\Html;
use Yii;
use yii\helpers\ArrayHelper;
use panix\engine\db\ActiveRecord;
use core\modules\shop\models\query\ManufacturerQuery;

/**
 * Class Manufacturer
 * @property integer $id
 * @property string $name
 * @property Product[] $productsCount
 *
 */
class Manufacturer extends ActiveRecord
{

    const MODULE_ID = 'shop';
    const route = '/admin/shop/manufacturer';

    /**
     * @inheritdoc
     * @return ManufacturerQuery
     */
    public static function find()
    {
        return new ManufacturerQuery(get_called_class());
    }

    public function getGridColumns()
    {
        return [
            'name' => [
                'attribute' => 'name',
                'format' => 'html',
                'contentOptions' => ['class' => 'text-left'],
                'value' => function ($model) {
                    return $model->name;
                }
            ],
            'products' => [
                'header' => static::t('PRODUCTS_COUNT'),
                'format' => 'html',
                'attribute' => 'productsCount',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return Html::a($model->productsCount, ['/admin/shop/product', 'ProductSearch[manufacturer_id]' => $model->id]);
                }
            ],
            'DEFAULT_CONTROL' => [
                'class' => 'panix\engine\grid\columns\ActionColumn',
            ],
            'DEFAULT_COLUMNS' => [
                [
                    'class' => \panix\engine\grid\sortable\Column::class,
                ],
                ['class' => 'panix\engine\grid\columns\CheckboxColumn'],
            ],
        ];
    }

    public static function getSort()
    {
        return new \yii\data\Sort([
            'attributes' => [
                //'date_create',
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                ],
            ],
        ]);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop__manufacturer}}';
    }


    /**
     * Products count relation
     * @return int|string
     */
    public function getProductsCount()
    {
        return $this->hasOne(Product::class, ['manufacturer_id' => 'id'])->count();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 255],
            [['ordern'], 'integer'],
            [['name'], 'safe'],
        ];
    }



}
