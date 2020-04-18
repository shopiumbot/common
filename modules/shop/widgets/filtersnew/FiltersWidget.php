<?php

namespace app\modules\shop\widgets\filtersnew;

use panix\engine\CMS;
use app\modules\shop\models\Attribute;
use yii\caching\DbDependency;
use yii\helpers\Html;
use Yii;
use app\modules\shop\models\Category;
use app\modules\shop\models\Product;
use app\modules\shop\models\Manufacturer;
use panix\engine\data\Widget;

/**
 * Class FiltersWidget
 * @package app\modules\shop\widgets\filtersnew
 */
class FiltersWidget extends Widget
{

    /**
     * @var array of Attribute models
     */
    public $attributes;
    //public $countAttr = true;
    //public $countManufacturer = true;
    //public $prices = [];
    public $tagCount = 'sup';
    public $tagCountOptions = ['class' => 'filter-count'];
    //public $showEmpty = false;


    /**
     * @var \app\modules\shop\models\query\CategoryQuery
     */
    public $model;

    /**
     * @var string min/max price in the query
     */
    protected $_currentPriceMin, $_currentPriceMax = null;
    public $priceMin, $priceMax;
    protected $prices = [];

    public function init()
    {
        $view = $this->getView();
        $this->priceMax = Yii::$app->controller->getMaxPrice();
        $this->priceMin = Yii::$app->controller->getMinPrice();


        if (Yii::$app->request->get('price')) {
            $this->prices = explode(',', Yii::$app->request->get('price'));
        }

        FilterAsset::register($view);
    }


    /**
     * @return array of attributes used in category
     */
    public function getCategoryAttributes()
    {
        $data = [];


        foreach ($this->attributes as $attribute) {
            $data[$attribute->name] = [
                'title' => $attribute->title,
                'selectMany' => (boolean)$attribute->select_many,
                'type' => (int)$attribute->type,
                'key' => $attribute->name,
                'filters' => []
            ];
            $totalCount = 0;
            $filtersCount = 0;
            foreach ($attribute->options as $option) {
                $count = $this->countAttributeProducts($attribute, $option);
                // if ($count > 1) {
                $data[$attribute->name]['filters'][] = [
                    'title' => $option->value,
                    'count' => (int)$count,
                    'data' => unserialize($option->data),
                    'abbreviation' => ($attribute->abbreviation) ? $attribute->abbreviation : null,
                    //  'queryKey' => $attribute->name,
                    'queryParam' => (int)$option->id,
                ];
                if ($count > 0)
                    $filtersCount++;

                $totalCount += $count;
                // }
            }

            $data[$attribute->name]['totalCount'] = $totalCount;
            $data[$attribute->name]['filtersCount'] = $filtersCount;
            if ($attribute->sort == SORT_ASC) {
                sort($data[$attribute->name]['filters']);
            } elseif ($attribute->sort == SORT_DESC) {
                rsort($data[$attribute->name]['filters']);
            }
        }
        return $data;
    }

    public function countAttributeProducts2($attribute, $option)
    {
        $model = Product::find();
        //$model->attachBehaviors($model->behaviors());
        $model->published();
        //$model->applyCategories($this->model);
        if ($this->model)
            $model->andWhere([Product::tableName() . '.main_category_id' => $this->model->id]);
        if (Yii::$app->request->get('min_price'))
            $model->applyMinPrice($this->convertCurrency(Yii::$app->request->getQueryParam('min_price')));

        if (Yii::$app->request->get('max_price'))
            $model->applyMaxPrice($this->convertCurrency(Yii::$app->request->getQueryParam('max_price')));

        if (Yii::$app->request->get('manufacturer'))
            $model->applyManufacturers(explode(',', Yii::$app->request->get('manufacturer')));

        //$data = array($attribute->name => $option->id);
        $current = $this->view->context->activeAttributes;

        $newData = [];

        foreach ($current as $key => $row) {
            if (!isset($newData[$key]))
                $newData[$key] = [];
            if (is_array($row)) {
                foreach ($row as $v)
                    $newData[$key][] = $v;
            } else
                $newData[$key][] = $row;
        }
        $newData[$attribute->name][] = $option->id;

        // echo $model->createCommand()->getRawSql();die;
        return $model->withEavAttributes($newData)->count();

    }

    public function countAttributeProducts($attribute, $option)
    {
        $model = Product::find()->published();
        //$model->attachBehaviors($model->behaviors());
        // $model->getEavAttributes22222222($this->view->context->getEavAttributes());
        if ($this->model instanceof Category) {
            $model->applyCategories($this->model);
            //$model->andWhere([Product::tableName() . '.main_category_id' => $this->model->id]);
        } elseif ($this->model instanceof Manufacturer) {
            $model->applyManufacturers($this->model->id);
        }


        if (Yii::$app->request->get('q') && Yii::$app->requestedRoute == 'shop/search/index') {
            $model->applySearch(Yii::$app->request->get('q'));
        }


        $newData = [];
        $newData[$attribute->name][] = $option->id;

        $res = $model->withEavAttributes($newData);
       // $res->groupBy = false;
        //$res->distinct(false);
        $res->cache(86400);

        // print_r($newData);die;
       // echo $res->createCommand()->rawSql;die;

        //$dependencyQuery = $model;
        //$dependencyQuery->select('COUNT(*)');
        //$dependency = new DbDependency([
        //     'sql' => $dependencyQuery->createCommand()->rawSql,
        //]);


        // $count = Attribute::getDb()->cache(function () use ($model) {
        //     return $model->count();
        // }, 1, $dependency);

        return $res->count();
    }

    public function run()
    {
        $manufacturers = $this->getCategoryManufacturers();


        $active = $this->view->context->getActiveFilters();


        echo Html::beginTag('div', ['id' => 'filters']);
        echo Html::beginForm($this->view->context->currentUrl, 'GET', ['id' => 'filter-form']);

        echo Html::beginTag('div', ['id' => 'ajax_filter_current']);
        if (!empty($active)) {
            $url = ($this->model) ? $this->model->getUrl() : ['/' . Yii::$app->requestedRoute];
            echo $this->render('current', ['active' => $active, 'dataModel' => $this->model, 'url' => $url]);
        }
        echo Html::endTag('div');
        echo $this->render('price');
        echo $this->render('attributes', ['attributes' => $this->getCategoryAttributes()]);
        echo $this->render('manufacturer', ['manufacturers' => $manufacturers]);
        echo Html::endForm();
        echo Html::endTag('div');
        $this->view->registerJs("
            $(function () {
                var selector = $('.card .card-collapse');
                selector.collapse({
                    toggle: false
                });
                var panels = $.cookie();
            
                for (var panel in panels) {
                    //console.log(panel);
                    if (panel) {
                        var panelSelector = $('#' + panel);
                        if (panelSelector) {
                            if (panelSelector.hasClass('card-collapse')) {
                                if ($.cookie(panel) === '1') {
                                    panelSelector.collapse('show');
                                } else {
                                    panelSelector.collapse('hide');
                                }
                            }
                        }
                    }
                }
            
                selector.on('show.bs.collapse', function () {
                    var active = $(this).attr('id');
                    $.cookie(active, '1');
            
                });
            
                selector.on('hide.bs.collapse', function () {
                    var active = $(this).attr('id');
                    $.cookie(active, null);
                });
            });
        ");


    }


    public function getCategoryManufacturers()
    {

        $query = Product::find();

        if ($this->model) {
            $query->applyCategories($this->model);
            //$query->andWhere([Product::tableName() . '.main_category_id' => $this->model->id]);
        }

        if (Yii::$app->request->get('q') && Yii::$app->requestedRoute == 'shop/search/index') {
            $query->applySearch(Yii::$app->request->get('q'));
        }
        $query->published();
        $queryClone = clone $query;
        $queryMan = $queryClone->addSelect(['manufacturer_id', Product::tableName() . '.id']);
        $queryMan->joinWith([
            'manufacturer' => function (\yii\db\ActiveQuery $query) {
                $query->andWhere([Manufacturer::tableName() . '.switch' => 1]);
            },
        ]);
        //$queryMan->->applyMaxPrice($this->convertCurrency(Yii::$app->request->getQueryParam('max_price')))
        //$queryMan->->applyMinPrice($this->convertCurrency(Yii::$app->request->getQueryParam('min_price')))

        $queryMan->andWhere('manufacturer_id IS NOT NULL');
        $queryMan->groupBy('manufacturer_id');


        // $manufacturers = $queryMan->all();


        $manufacturers = Manufacturer::getDb()->cache(function ($db) use ($queryMan) {
            return $queryMan
                //->joinWith('translations as translate')
                //->orderBy(['translate.name'=>SORT_ASC])
                ->all();
        }, 86400);


        //$manufacturers =$queryMan->all();
        //echo $q->createCommand()->rawSql;die;
        $data = [
            'title' => Yii::t('shop/default', 'FILTER_BY_MANUFACTURER'),
            'selectMany' => true,
            'filters' => []
        ];

        if ($manufacturers) {

            foreach ($manufacturers as $m) {

                $m = $m->manufacturer;

                if ($m) {
                    $query = Product::find();
                    $query->published();
                    if ($this->model) {
                        $query->applyCategories($this->model);
                        //$query->andWhere([Product::tableName() . '.main_category_id' => $this->model->id]);
                    }

                    //$q->applyMinPrice($this->convertCurrency(Yii::app()->request->getQuery('min_price')))
                    //$q->applyMaxPrice($this->convertCurrency(Yii::app()->request->getQuery('max_price')))
                    $query->applyManufacturers($m->id);

                    if (Yii::$app->request->get('q') && Yii::$app->requestedRoute == 'shop/search/index') {
                        $query->applySearch(Yii::$app->request->get('q'));
                    }


                    /*$dependencyQuery = $query;
                    $dependencyQuery->select('COUNT(*)');
                    $dependency = new DbDependency([
                        'sql' => $dependencyQuery->createCommand()->rawSql,
                    ]);

                    $count = Product::getDb()->cache(function () use ($query) {
                        return $query->count();
                    }, 3600 * 24, $dependency);*/


                    $query->orderBy = false;
                    $count = $query->cache(86400)->count();

                    $data['filters'][] = [
                        'title' => $m->name,
                        'count' => (int)$count,
                        'key' => 'manufacturer',
                        'queryParam' => $m->id,
                    ];
                    sort($data['filters']);
                } else {
                    die('err manufacturer');
                }
            }
        }

        return $data;
    }

    public function convertCurrency($sum)
    {
        $cm = Yii::$app->currency;
        if ($cm->active->id != $cm->main->id)
            return $cm->activeToMain($sum);
        return $sum;
    }

    public function getCount($filter)
    {
        $result = ($filter['count'] > 0) ? $filter['count'] : 0;
        return Html::tag($this->tagCount, $result, $this->tagCountOptions);
    }

    public function generateGradientCss($data)
    {
        $css = '';
        if ($data) {
            $css .= "background: {$data[0]['color']};";
            if (count($data) > 1) {

                $res_data = [];
                foreach ($data as $k => $color) {
                    $res_data[] = $color['color'];
                }
                $res = implode(', ', $res_data);

                if (count($data) == 2) {
                    $value = "45deg, {$data[0]['color']} 50%, {$data[1]['color']} 50%";
                    $css .= "background: -moz-linear-gradient({$value});";
                    $css .= "background: -webkit-linear-gradient({$value});";
                    $css .= "background: linear-gradient({$value});";
                } elseif (count($data) == 3) {
                    $value = "45deg, {$data[0]['color']} 0%, {$data[0]['color']} 33%, {$data[1]['color']} 33%, {$data[1]['color']} 66%, {$data[2]['color']} 66%, {$data[2]['color']} 100%";
                    $css .= "background: -moz-linear-gradient({$value});";
                    $css .= "background: -webkit-linear-gradient({$value});";
                    $css .= "background: linear-gradient({$value});";
                } elseif (count($data) == 4) {
                    $value = "45deg, {$data[0]['color']} 0%, {$data[0]['color']} 25%, {$data[1]['color']} 25%, {$data[1]['color']} 50%, {$data[2]['color']} 50%, {$data[2]['color']} 75%, {$data[3]['color']} 75%, {$data[3]['color']} 100%";
                    $css .= "background: -moz-linear-gradient({$value});";
                    $css .= "background: -webkit-linear-gradient({$value});";
                    $css .= "background: linear-gradient({$value});";
                } elseif (count($data) >= 4) {
                    $css .= "background: -moz-radial-gradient(farthest-corner at 0% 100%, {$res});";
                    $css .= "background: -webkit-radial-gradient(farthest-corner at 0% 100%, {$res});";
                    $css .= "background: radial-gradient(farthest-corner at 0% 100%, {$res});";
                }
                $css .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$data[0]['color']}', endColorstr='{$data[1]['color']}',GradientType=1 );";
            }
        }
        return $css;
    }


    /**
     * @return mixed
     */
    public function getCurrentMinPrice()
    {
        if ($this->_currentPriceMin !== null)
            return $this->_currentPriceMin;

        $this->_currentPriceMin = (isset($this->prices[0])) ? $this->prices[0] : Yii::$app->currency->convert($this->priceMin);

        return $this->_currentPriceMin;
    }

    /**
     * @return mixed
     */
    public function getCurrentMaxPrice()
    {
        if ($this->_currentPriceMax !== null)
            return $this->_currentPriceMax;

        $this->_currentPriceMax = (isset($this->prices[1])) ? $this->prices[1] : Yii::$app->currency->convert($this->priceMax);

        return $this->_currentPriceMax;
    }
}
