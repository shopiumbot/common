<?php

namespace core\modules\shop\models\traits;

use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use core\modules\shop\models\Category;
use core\modules\shop\models\Manufacturer;
use core\modules\shop\models\ProductType;
use core\modules\shop\models\search\ProductSearch;
use core\modules\shop\models\Supplier;
use panix\engine\Html;
use panix\engine\CMS;
use core\modules\shop\models\Attribute;
use core\modules\shop\models\Product;

/**
 * Trait ProductTrait
 * @package core\modules\shop\models\traits
 */
trait ProductTrait
{
    public static function getAvailabilityItems()
    {
        return [
            Product::AVAILABILITY_YES => self::t('AVAILABILITY_1'),
            Product::AVAILABILITY_ORDER => self::t('AVAILABILITY_2'),
            Product::AVAILABILITY_NOT => self::t('AVAILABILITY_3'),
        ];
    }

    public function getGridColumns()
    {
		
		$price_max = Product::find()->aggregatePrice('MAX')->asArray()->one();
        $price_min = Product::find()->aggregatePrice('MIN')->asArray()->one();

        $columns = [];
        $columns['image'] = [
            'class' => 'panix\engine\grid\columns\ImageColumn',
            'attribute' => 'image',
            // 'filter'=>true,
            'value' => function ($model) {
                /** @var $model Product */
                return $model->renderGridImage();
            },
        ];
        $columns['name'] = [
            'attribute' => 'name',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                /** @var $model Product */
                if ($model->name) {
                    $html = $model->name;
                    if (true) {
                        $labels = [];
                        foreach ($model->labels() as $label) {
                            $labelOptions = [];
                            $labelOptions['class'] = 'badge badge-' . $label['class'];
                            if (isset($label['tooltip']))
                                $labelOptions['title'] = $label['tooltip'];
                            $labelOptions['data-toggle'] = 'tooltip';
                            $labels[] = Html::tag('span', $label['value'], $labelOptions);
                        }
                        $html .= '<br/>' . implode('', $labels);
                    }
                    return $html;
                }
                return null;

            },
        ];
        $columns['sku'] = [
            'attribute' => 'sku',
        ];
        $columns['type_id'] = [
            'attribute' => 'type_id',
        ];
        /*$columns['price'] = [
            'attribute' => 'price',
            'format' => 'raw',
            'class' => 'panix\engine\grid\columns\jui\SliderColumn',
            'max' => (int)Product::find()->aggregatePrice('MAX'),
            'min' => (int)Product::find()->aggregatePrice('MIN'),
            'prefix' => '<sup>' . Yii::$app->currency->main['symbol'] . '</sup>',
            'contentOptions' => ['class' => 'text-center', 'style' => 'position:relative'],
            'value' => function ($model) {
                $ss = '';
                if ($model->hasDiscount) {
                    $price = $model->discountPrice;
                    $ss = '<del class="text-secondary">' . Yii::$app->currency->number_format($model->originalPrice) . '</del> / ';
                } else {
                    $price = $model->price;
                }
                if ($model->currency_id) {
                    $priceHtml = $price;
                    $symbol = Html::tag('sup', Yii::$app->currency->currencies[$model->currency_id]['symbol']);
                } else {
                    $priceHtml = Yii::$app->currency->convert($price, $model->currency_id);
                    $symbol = Html::tag('sup', Yii::$app->currency->main['symbol']);
                }
                //$ss .= '<span class="badge badge-danger position-absolute" style="top:0;right:0;">123</span>';
                return $ss . Html::tag('span', Yii::$app->currency->number_format($priceHtml), ['class' => 'text-success font-weight-bold']) . ' ' . $symbol;
            }
        ];*/
        $columns['price'] = [
            'attribute' => 'price',
            'format' => 'raw',
            'class' => 'panix\engine\grid\columns\jui\SliderColumn',
            'max' => (int)$price_max['aggregation_price'],
            'min' => (int)$price_min['aggregation_price'],
            'prefix' => '<sup>' . Yii::$app->currency->main['symbol'] . '</sup>',
            'contentOptions' => ['class' => 'text-center', 'style' => 'position:relative'],
            'value' => function ($model) {

                $prices = [];
                $newprice = [];
                /** @var $model Product */
                $discount = '';
                if ($model->hasDiscount) {
                    $price = $model->discountPrice;
                    //$priceCurrency = $model->discountPrice * Yii::$app->currency->currencies[$model->currency_id]['rate'];
                    //$discount = '<del class="text-secondary">' . Yii::$app->currency->number_format($model->originalPrice) . '</del> / ';
                    if ($model->currency_id) {
                        $newprice[$model->currency_id]['price'] = $model->discountPrice;
                        $newprice[$model->currency_id]['discount_price'] = $model->price;


                        $newprice[$model->currency_id][Yii::$app->currency->main['iso']]['price'] = $model->discountPrice * Yii::$app->currency->currencies[$model->currency_id]['rate'];
                        $newprice[$model->currency_id][Yii::$app->currency->main['iso']]['discount_price'] = $model->price * Yii::$app->currency->currencies[$model->currency_id]['rate'];
                        $newprice[$model->currency_id][Yii::$app->currency->main['iso']]['symbol'] = Yii::$app->currency->main['symbol'];
                    } else {
                        // $newprice[$model->currency_id]['price'] = $model->price;
                    }

                } else {
                    if ($model->currency_id) {
                        $newprice[$model->currency_id]['price'] = $model->price;
                        $newprice[$model->currency_id][Yii::$app->currency->main['iso']]['price'] = $model->price * Yii::$app->currency->currencies[$model->currency_id]['rate'];
                        $newprice[$model->currency_id][Yii::$app->currency->main['iso']]['symbol'] = Yii::$app->currency->main['symbol'];
                    } else {
                        $newprice[0]['price'] = $model->price;
                    }

                    $price = $model->price;
                    //  $priceCurrency = $model->price*Yii::$app->currency->currencies[$model->currency_id]['rate'];
                }
                if ($model->currency_id) {
                    $newprice[$model->currency_id]['symbol'] = Yii::$app->currency->currencies[$model->currency_id]['symbol'];

                     $newprice[$model->currency_id]['price']=$price;

                    // $symbol = Html::tag('span', Yii::$app->currency->currencies[$model->currency_id]['symbol']);
                    // $symbol2 = Html::tag('span', Yii::$app->currency->currencies[1]['symbol']);
                    //  $prices[] = Html::tag('span', Yii::$app->currency->number_format($price), ['class' => 'text-success font-weight-bold']).' '.$symbol;
                    //  $prices[] = Html::tag('span', Yii::$app->currency->number_format($priceCurrency), ['class' => 'text-success font-weight-bold']).' '.$symbol2;
                    //  $priceHtml = implode('<br/>',$prices);

                } else {
                    $newprice[0]['symbol'] = Yii::$app->currency->main['symbol'];
                    $newprice[0]['price'] = $model->price;
                    $symbol = Html::tag('sup', Yii::$app->currency->main['symbol']);
                    $priceHtml = Html::tag('span', Yii::$app->currency->number_format(Yii::$app->currency->convert($price, $model->currency_id)), ['class' => 'text-success font-weight-bold']) . ' ' . $symbol;

                }


               // CMS::dump($newprice);//die;
                $html='';
                $data=[];
                foreach ($newprice as $currency=>$price_data){
                    $price='';

                    if(isset($price_data['discount_price'])){
                        $price.='<del class="text-secondary">'.Yii::$app->currency->number_format($price_data['discount_price']).'</del> / ';
                    }
                    $price.=Html::tag('span', Yii::$app->currency->number_format($price_data['price']), ['class' => 'text-success font-weight-bold']).' '.$price_data['symbol'];

                    $data[]=$price;

                    $pricesub='';
                    if(isset($price_data[Yii::$app->currency->main['iso']])){
                        $subPrice = $price_data[Yii::$app->currency->main['iso']];

                        if(isset($subPrice['discount_price'])){
                          //  $pricesub.='<del class="text-secondary">'.Yii::$app->currency->number_format($subPrice['discount_price']).'</del> / ';
                        }

                        //foreach ($price_data[Yii::$app->currency->main['iso']] as $price1){
                        $pricesub.=Html::tag('span', Yii::$app->currency->number_format($subPrice['price']), ['class' => 'text-success font-weight-bold']).' '.$price_data[Yii::$app->currency->main['iso']]['symbol'];
                        //}

                        $data[] = $pricesub;
                       // if(isset($price_data[Yii::$app->currency->main['iso']]['old_price'])){
                       //     $data[]=Html::tag('span', Yii::$app->currency->number_format($price_data['old_price']), ['class' => 'text-success font-weight-bold']).' '.$price_data['symbol'];
                       // }
                    }

                }


                //$ss .= '<span class="badge badge-danger position-absolute" style="top:0;right:0;">123</span>';
                return implode('<br>',$data);
            }
        ];

        $columns['manufacturer_id'] = [
            'attribute' => 'manufacturer_id',
            'filter' => ArrayHelper::map(Manufacturer::find()
               // ->addOrderBy(['name'=>SORT_ASC])
                //->cache(3200, new DbDependency(['sql' => 'SELECT MAX(`updated_at`) FROM ' . Manufacturer::tableName()]))
                ->all(), 'id', 'name'),
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => html_entity_decode('&mdash; выберите производителя &mdash;')],
            'value' => function ($model) {
                return ($model->manufacturer) ? $model->manufacturer->name : NULL;
            }
        ];
        $columns['categories'] = [
            'header' => static::t('Категории'),
            'attribute' => 'main_category_id',
            'format' => 'html',
            'contentOptions' => ['style' => 'max-width:180px'],
            'filter' => Html::dropDownList(Html::getInputName(new ProductSearch, 'main_category_id'), (isset(Yii::$app->request->get('ProductSearch')['main_category_id'])) ? Yii::$app->request->get('ProductSearch')['main_category_id'] : null, Category::flatTree(),
                [
                    'class' => 'form-control',
                    'prompt' => html_entity_decode('&mdash; выберите категорию &mdash;')
                ]
            ),
            'value' => function ($model) {
                /** @var $model Product */
                $result = '';
                foreach ($model->categories as $category) {
                    $options['data-pjax'] = 0;
                    if ($category->id == $model->main_category_id) {
                        $options['class'] = 'badge badge-secondary';
                        $options['title'] = $category->name;
                    } else {
                        $options['class'] = 'badge badge-light';
                    }
                    $result .= Html::tag('span',$category->name, $options);
                }
                return $result;
            }
        ];
        $columns['created_at'] = [
            'attribute' => 'created_at',
            'class' => 'panix\engine\grid\columns\jui\DatepickerColumn',
        ];
        $columns['updated_at'] = [
            'attribute' => 'updated_at',
            'class' => 'panix\engine\grid\columns\jui\DatepickerColumn',
        ];

        $columns['DEFAULT_CONTROL'] = [
            'class' => 'panix\engine\grid\columns\ActionColumn',
        ];
        $columns['DEFAULT_COLUMNS'] = [
            [
                'class' => \panix\engine\grid\sortable\Column::class,
            ],
            [
                'class' => 'panix\engine\grid\columns\CheckboxColumn',
                'customActions' => [
                    [
                        'label' => self::t('GRID_OPTION_ACTIVE'),
                        'url' => '#',
                        'icon' => 'eye',
                        'linkOptions' => [
                            'onClick' => 'return setProductsStatus(1, this);',
                            'data-confirm' => self::t('CONFIRM_SHOW'),
                            'data-pjax' => 0
                        ],
                    ],
                    [
                        'label' => self::t('GRID_OPTION_DEACTIVE'),
                        'url' => '#',
                        'icon' => 'eye-close',
                        'linkOptions' => [
                            'onClick' => 'return setProductsStatus(0, this);',
                            'data-confirm' => self::t('CONFIRM_HIDE'),
                            'data-pjax' => 0
                        ],
                    ],
                    [
                        'label' => self::t('GRID_OPTION_SETCATEGORY'),
                        'url' => '#',
                        'icon' => 'folder-open',
                        'linkOptions' => [
                            'onClick' => 'return showCategoryAssignWindow(this);',
                           // 'data-confirm' => self::t('CONFIRM_CATEGORY'),
                            'data-pjax' => 0
                        ],
                    ],
                    [
                        'label' => self::t('GRID_OPTION_COPY'),
                        'url' => '#',
                        'icon' => 'copy',
                        'linkOptions' => [
                            'onClick' => 'return showDuplicateProductsWindow(this);',
                           // 'data-confirm' => self::t('CONFIRM_COPY'),
                            'data-pjax' => 0
                        ],
                    ],
                    [
                        'label' => self::t('GRID_OPTION_SETPRICE'),
                        'url' => '#',
                        'icon' => 'currencies',
                        'linkOptions' => [
                            'onClick' => 'return setProductsPrice(this);',
                            //'data-confirm' => self::t('CONFIRM_PRICE'),
                            'data-pjax' => 0
                        ],
                    ],
                ]
            ]
        ];

        return $columns;
    }


    public function getDataAttributes()
    {


        /** @var \core\modules\shop\components\EavBehavior $attributes */
        $attributes = $this->getEavAttributes();
        $data = [];
        $groups = [];
        $models = [];

        // $query = Attribute::getDb()->cache(function () {
        $query = Attribute::find()
            ->where(['name'=>array_keys($attributes)])
            ->sort()
            ->all();
        // }, 3600);


        foreach ($query as $m)
            $models[$m->name] = $m;


        foreach ($models as $model) {
            /** @var Attribute $model */
            $abbr = ($model->abbreviation) ? ' ' . $model->abbreviation : '';

            $value = $model->renderValue($attributes[$model->name]) . $abbr;

            // $data[$model->title] = $value;
            $data[$model->name]['name'] = $model->title;
            $data[$model->name]['value'] = $value;

        }

        return [
            'data' => $data,
            'groups' => $groups,
        ];
    }

    /**
     * Convert price to current currency
     *
     * @param string $attr
     * @return mixed
     */
    public function toCurrentCurrency($attr = 'price')
    {
        return Yii::$app->currency->convert($this->$attr);
    }

    public function getProductAttributes()
    {
        /** @var $this Product */
        //Yii::import('mod.shop.components.AttributesRender');
        $attributes = new \core\modules\shop\components\AttributesRender;
        return $attributes->getData($this);
    }


}
