<?php

namespace core\modules\shop\models;


use core\modules\shop\components\ExternalFinder;
use shopium\mod\cart\models\OrderProduct;
use shopium\mod\discounts\components\DiscountBehavior;
use core\modules\images\models\Image;
use panix\mod\user\models\User;
use Yii;
use panix\engine\CMS;
use core\modules\shop\models\query\ProductQuery;
use yii\caching\DbDependency;
use yii\caching\TagDependency;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use core\components\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class Product
 * @property integer $id Product id
 * @property integer $manufacturer_id Manufacturer
 * @property integer $type_id Type
 * @property integer $currency_id Currency
 * @property Currency $currency
 * @property string $name Product name
 * @property float $price Price
 * @property float $max_price Max price
 * @property boolean $unit Unit
 * @property boolean $sku Product article
 * @property integer $quantity
 * @property integer $availability
 * @property integer $label
 * @property integer $main_category_id
 * @property integer $auto_decrease_quantity
 * @property integer $created_at
 * @property integer $updated_at
 * @property boolean $switch
 * @property integer $added_to_cart_count
 * @property integer $votes
 * @property integer $rating
 * @property Manufacturer[] $manufacturer
 * @property string $discount Discount
 * @property boolean $hasDiscount See [[\shopium\mod\discounts\components\DiscountBehavior]] //Discount
 * @property float $originalPrice See [[\shopium\mod\discounts\components\DiscountBehavior]]
 * @property float $discountPrice See [[\shopium\mod\discounts\components\DiscountBehavior]]
 * @property integer $ordern
 * @property boolean $isAvailable
 * @property Category $categories
 * @property array $eavAttributes
 * @property ProductPrices[] $prices
 * @property ProductType $type
 */
class Product extends ActiveRecord
{

    use traits\ProductTrait;


    public $file;

    const route = '/admin/shop/default';
    const MODULE_ID = 'shop';

    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    public function labels()
    {
        /** @var DiscountBehavior|self $this */

        $labelsList['new'] = [
            'class' => 'success',
            'value' => self::t('LABEL_NEW')
        ];
        $labelsList['top_sale'] = [
            'class' => 'success',
            'value' => self::t('LABEL_TOP_SALE')
        ];
        $labelsList['sale'] = [
            'class' => 'primary',
            'value' => self::t('LABEL_SALE')
        ];
        $labelsList['discount'] = [
            'class' => 'danger',
            'value' => self::t('LABEL_DISCOUNT')
        ];

        $result = [];
        $new = Yii::$app->settings->get('app', 'label_expire_new');
        if ($this->label == 1) {
            $result['new'] = $labelsList['new'];
        } elseif ($this->label == 2) {
            $result['sale'] = $labelsList['sale'];
        } elseif ($this->label == 3) {
            $result['discount'] = $labelsList['discount'];
        } elseif ($this->label == 4) {

        }
        if ($new) {
            if ((time() - 86400 * $new) <= $this->created_at) {
                $result['new'] = [
                    'class' => 'success',
                    'value' => self::t('LABEL_NEW'),
                    // 'title' => Yii::t('app/default', 'FROM_BY', Yii::$app->formatter->asDate(date('Y-m-d', $this->created_at))) . ' ' . Yii::t('app/default', 'TO_BY', Yii::$app->formatter->asDate(date('Y-m-d', $this->created_at + (86400 * $new))))
                ];
            }
        }

        if (isset($this->hasDiscount)) {
            $result['discount']['class'] = 'danger';
            $result['discount']['value'] = '-' . $this->discountSum;
            if ($this->discountEndDate) {
                $result['discount']['title'] = '-' . $this->discountSum . ' до ' . $this->discountEndDate;
            }
        }
        return $result;
    }

    public function getIsAvailable()
    {
        return $this->availability == 1;
    }

    public static function getSort()
    {
        return new \yii\data\Sort([
            //'defaultOrder'=>'ordern DESC',
            'attributes' => [
                '*',
                'price' => [
                    'asc' => ['price' => SORT_ASC],
                    'desc' => ['price' => SORT_DESC],
                    //'default' => SORT_ASC,
                    //'label' => 'Цена1',
                ],
                'sku' => [
                    'asc' => ['sku' => SORT_ASC],
                    'desc' => ['sku' => SORT_DESC],
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => 'по дате добавления'
                ],
                'name' => [
                    'default' => SORT_ASC,
                    'asc' => ['translation.name' => SORT_ASC],
                    'desc' => ['translation.name' => SORT_DESC],
                ],
            ],
        ]);
    }


    public function getMainImage($size = false)
    {
        /** @var $image \core\modules\images\behaviors\ImageBehavior|\core\modules\images\models\Image */
        $image = $this->getImage();
        $result = [];
        if ($image) {
            $result['url'] = $image->getUrl($size);
            $result['title'] = $this->name;
        } else {
            $result['url'] = CMS::placeholderUrl(['size' => $size]);
            $result['title'] = $this->name;
        }

        return (object)$result;
    }

    /**
     * @param string $size Default value 50x50.
     * @return string
     */
    public function renderGridImage($size = '50x50')
    {
        $small = $this->getMainImage($size);
        $big = $this->getMainImage();
        return Html::a(Html::img($small->url, ['alt' => $small->title, 'class' => 'img-thumbnail']), $big->url, ['title' => $this->name, 'data-fancybox' => 'gallery']);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop__product}}';
    }

    /* public function transactions() {
      return [
      self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
      ];
      } */


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['duplicate'] = [];
        return $scenarios;
    }

    /**
     * Decrease product quantity when added to cart
     */
    public function decreaseQuantity()
    {
        if ($this->auto_decrease_quantity && (int)$this->quantity > 0) {
            $this->quantity--;
            $this->save(false);
        }
    }

    public static function labelsList()
    {
        return [
            1 => self::t('LABEL_NEW'),
            2 => self::t('LABEL_SALE'),
            3 => self::t('LABEL_DISCOUNT')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        $rules = [];


        $rules[] = [['main_category_id', 'price', 'unit', 'name'], 'required'];
        $rules[] = ['price', 'commaToDot'];
        $rules[] = [['file'], 'file', 'maxFiles' => Yii::$app->params['plan'][Yii::$app->user->planId]['product_upload_files']];
        $rules[] = [['file'], 'validateLimit'];
        $rules[] = [['name'], 'string', 'max' => 255];
        $rules[] = [['image'], 'image'];
        $rules[] = [['name'], 'unique'];
        $rules[] = [['name'], 'trim'];
        $rules[] = [['description'], 'string'];
        $rules[] = [['unit'], 'default', 'value' => 1];
        $rules[] = [['sku', 'description', 'label', 'discount'], 'default']; // установим ... как NULL, если они пустые
        $rules[] = [['price'], 'double'];
        $rules[] = [['manufacturer_id', 'type_id', 'quantity', 'availability', 'added_to_cart_count', 'ordern', 'currency_id', 'label'], 'integer'];
        $rules[] = [['name', 'description'], 'safe'];

        return $rules;
    }

    public function validateLimit($attribute)
    {
        $planCount = Yii::$app->params['plan'][Yii::$app->user->planId]['product_upload_files'];
        $imageCount = Image::find()->where([
            'product_id' => $this->primaryKey,
        ])->count();


        $files = $_FILES[(new \ReflectionClass($this))->getShortName()];

        if (isset($files['name'])) {
            if (!empty($files['name']['file'][0])) {
                $imageCount += count($files['name']['file']);
            }
        }

        if (($imageCount > $planCount)) {
            $this->addError($attribute, Yii::t('app/default', 'Привышен лимит изображений, доступно всего {0}', $planCount));
        }
    }

    public function getUnits()
    {
        return [
            1 => self::t('UNIT_THING'),
            2 => self::t('UNIT_METER'),
            3 => self::t('UNIT_BOX'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->cache(3600);
    }


    public function getManufacturer()
    {
        return $this->hasOne(Manufacturer::class, ['id' => 'manufacturer_id']);
        //->cache(3200, new DbDependency(['sql' => 'SELECT MAX(`updated_at`) FROM ' . Manufacturer::tableName()]));
    }


    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'type_id']);

    }

    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);

    }

    public function getType2()
    {
        return $this->hasOne(ProductType::class, ['type_id' => 'id']);
    }


    public function getCategorization()
    {
        return $this->hasMany(ProductCategoryRef::class, ['product' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category'])->via('categorization');
    }

    public function getPrices()
    {
        return $this->hasMany(ProductPrices::class, ['product_id' => 'id']);
    }

    public function getMainCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category'])
            ->via('categorization', function ($query) {
                /** @var Query $query */
                $query->where(['is_main' => 1]);
            });
    }


//'variants' => array(self::HAS_MANY, 'ProductVariant', array('product_id'), 'with' => array('attribute', 'option'), 'order' => 'option.ordern'),

    /**
     * @param array $prices
     */
    public function processPrices(array $prices)
    {
        $dontDelete = [];

        foreach ($prices as $index => $price) {
            if ($price['value'] > 0) {

                $record = ProductPrices::find()->where(array(
                    'id' => $index,
                    'product_id' => $this->id,
                ))->one();

                if (!$record) {
                    $record = new ProductPrices;
                }
                $record->from = $price['from'];
                $record->value = $price['value'];
                $record->product_id = $this->id;
                $record->save();

                $dontDelete[] = $record->id;
            }
        }

        // Delete not used relations
        if (sizeof($dontDelete) > 0) {
            ProductPrices::deleteAll(
                ['AND', 'product_id=:id', ['NOT IN', 'id', $dontDelete]], [':id' => $this->id]);
        } else {
            // Delete all relations
            ProductPrices::deleteAll('product_id=:id', [':id' => $this->id]);
        }

    }

    /**
     * Set product categories and main category
     * @param array $categories ids.
     * @param integer $main_category Main category id.
     */
    public function setCategories(array $categories, $main_category)
    {
        $notDelete = [];


        if (!Category::find()->where(['id' => $main_category])->count())
            $main_category = 1;

        if (!in_array($main_category, $categories))
            array_push($categories, $main_category);


        foreach ($categories as $category) {

            $count = ProductCategoryRef::find()->where([
                'category' => (int)$category,
                'product' => $this->id,
            ])->count();


            if (!$count) {
                $record = new ProductCategoryRef;
                $record->category = (int)$category;
                $record->product = $this->id;
                if ($this->scenario == 'duplicate') {
                    $record->switch = 1;
                } else {
                    $record->switch = ($this->switch) ? $this->switch : 1;
                }
                $record->save(false);
            }

            $notDelete[] = (int)$category;
        }

        // Clear main category
        ProductCategoryRef::updateAll([
            'is_main' => 0,
            'switch' => $this->switch
        ], 'product=:p', [':p' => $this->id]);

        // Set main category
        ProductCategoryRef::updateAll([
            'is_main' => 1,
            'switch' => $this->switch,
        ], 'product=:p AND category=:c', [':p' => $this->id, ':c' => $main_category]);

        // Delete not used relations
        if (count($notDelete) > 0) {

            ProductCategoryRef::deleteAll(
                ['AND', 'product=:id', ['NOT IN', 'category', $notDelete]], [':id' => $this->id]);

        } else {
            // Delete all relations
            ProductCategoryRef::deleteAll(['product' => $this->id]);
        }

    }

    public function getFrontPrice()
    {
        if ($this->hasDiscount) {
            $price = $this->discountPrice;
        } else {
            $price = $this->price;
        }
        return $price;
    }

    public function priceRange()
    {
        $price = $this->getFrontPrice();
        $max_price = Yii::$app->currency->convert($this->max_price);

        if ($max_price > 0)
            return Yii::$app->currency->number_format($price) . ' - ' . Yii::$app->currency->number_format($max_price);

        return Yii::$app->currency->number_format($price);
    }

    public function afterDelete()
    {
        // Delete categorization
        ProductCategoryRef::deleteAll([
            'product' => $this->id
        ]);

        $external = new ExternalFinder('{{%csv}}');
        $external->removeByObject(ExternalFinder::OBJECT_PRODUCT, $this->id);

        OrderProduct::deleteAll([
            'product_id' => $this->id
        ]);

        parent::afterDelete();
    }


    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (substr($name, 0, 4) === 'eav_') {

            $table = Attribute::tableName();
            $dependency = new DbDependency();
            $dependency->sql = "SELECT MAX(updated_at) FROM {$table}";


            if ($this->getIsNewRecord())
                return null;

            $attribute = substr($name, 4);
            /** @var \core\modules\shop\components\EavBehavior $this */
            $eavData = $this->getEavAttributes();

            if (isset($eavData[$attribute]))
                $value = $eavData[$attribute];
            else
                return null;

            /** @var Attribute $attributeModel */
            $attributeModel = Attribute::getDb()->cache(function ($db) use ($attribute) {
                $q = Attribute::find()->where(['name' => $attribute]);

                $result = $q->one();
                return $result;
            });


            //$attributeModel = Attribute::find()->where(['name' => $attribute])->cache(3600 * 24, $dependency)->one();
            return ['name' => $attributeModel->title, 'value' => $attributeModel->renderValue($value)];
        }
        return parent::__get($name);
    }


    public function behaviors()
    {
        $a = [];
        // if (Yii::$app->getModule('images'))
        $a['imagesBehavior'] = [
            'class' => '\core\modules\images\behaviors\ImageBehavior',
            'path' => '@uploads/store/product'
        ];
        $a['eav'] = [
            'class' => '\core\modules\shop\components\EavBehavior',
            'tableName' => '{{%shop__product_attribute_eav}}'
        ];

        if (Yii::$app->getModule('discounts'))
            $a['discounts'] = [
                'class' => '\shopium\mod\discounts\components\DiscountBehavior'
            ];

        return ArrayHelper::merge($a, parent::behaviors());
    }

    /*public static function formatPrice($price)
    {
        $c = Yii::$app->settings->get('shop');
        return iconv("windows-1251", "UTF-8", number_format($price, $c->price_penny, $c->price_thousand, $c->price_decimal));
    }*/

    /**
     * Replaces comma to dot
     * @param $attr
     */
    public function commaToDot($attr)
    {
        $this->$attr = str_replace(',', '.', $this->$attr);
    }

    public function getPriceByQuantity($q = 1)
    {
        return ProductPrices::find()
            ->where(['product_id' => $this->id])
            ->andWhere(['<=', 'from', $q])
            ->orderBy(['from' => SORT_DESC])
            ->one();
    }

    /**
     * @param $product Product
     * @param int $quantity
     * @return float|int|mixed|null
     */
    public static function calculatePrices($product, $quantity = 1)
    {
        // print_r($product);die;
        if (($product instanceof Product) === false)
            $product = Product::findOne($product);


        // if ($quantity > 1 && ($pr = $product->getPriceByQuantity($quantity))) {
        if ($product->prices && $quantity > 1) {
            $pr = $product->getPriceByQuantity($quantity);
            $result = $pr->value;
            // if ($product->currency_id) {
            //$result = Yii::$app->currency->convert($pr->value, $product->currency_id);
            //} else {
            //     $result = $pr->value;
            //}
        } else {
            if ($product->currency_id) {
                $result = Yii::$app->currency->convert($product->hasDiscount ? $product->discountPrice : $product->price, $product->currency_id);
            } else {
                $result = Yii::$app->currency->convert($product->hasDiscount ? $product->discountPrice : $product->price, $product->currency_id);
                //$result = ($product->hasDiscount) ? $product->discountPrice : $product->price;
            }

        }


        return $result;
    }


}
