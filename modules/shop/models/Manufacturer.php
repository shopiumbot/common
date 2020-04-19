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
 * @property string $name ManufacturerTranslate
 * @property string $description
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
            'image' => [
                'class' => 'panix\engine\grid\columns\ImageColumn',
                'attribute' => 'image',
                'value' => function ($model) {
                    return Html::a(Html::img($model->getImageUrl('image', '50x50'), ['alt' => $model->name, 'class' => 'img-thumbnail_']), $model->getImageUrl('image'), ['title' => $model->name, 'data-fancybox' => 'gallery']);
                }
            ],
            'name' => [
                'attribute' => 'name',
                'format' => 'html',
                'contentOptions' => ['class' => 'text-left'],
                'value' => function ($model) {
                    return Html::a($model->name, $model->getUrl(), ['target' => '_blank']);
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
            [['description'], 'string'],
            [['description', 'image'], 'default'],
            [['name'], 'string', 'max' => 255],
            [['ordern'], 'integer'],
            [['name'], 'safe'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $a = [];
        $a['uploadFile'] = [
            'class' => 'panix\engine\behaviors\UploadFileBehavior',
            'files' => [
                'image' => '@uploads/manufacturer',
            ],
            'options' => [
                'watermark' => false
            ]
        ];

        return ArrayHelper::merge($a, parent::behaviors());
    }

}
