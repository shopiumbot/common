<?php

namespace core\modules\shop\controllers;


use Yii;
use yii\helpers\Url;
use yii\web\Response;
use core\modules\shop\models\Manufacturer;
use core\modules\shop\models\Product;
use core\modules\shop\components\FilterController;

class ManufacturerController extends FilterController
{

    public $provider;
    public $currentUrl;
    public function actionIndex()
    {
        $model = Manufacturer::find()->published()->all();
        $this->currentUrl = '/';
        $this->pageName = Yii::t('shop/default','MANUFACTURER');
        $this->breadcrumbs[] = $this->pageName;
        return $this->render('index', ['model' => $model]);
    }

    /**
     * Display products by manufacturer
     * @param $slug
     * @return string
     */
    public function actionView($slug)
    {

        $this->findModel($slug);
       // $this->currentUrl = Url::to($this->dataModel->getUrl());
        /** @var Product $productModel */
        $productModel = Yii::$app->getModule('shop')->model('Product');
        $this->query = $productModel::find();
        //$this->query->attachBehaviors((new $productModel)->behaviors());
        $this->query->published();
        $this->query->applyManufacturers($this->dataModel->id);
        $this->query->applyAttributes($this->activeAttributes);

        $this->currentQuery = clone $this->query;

        $this->applyPricesFilter();
        $this->pageName = $this->dataModel->name;
        $this->view->setModel($this->dataModel);
        $this->view->title = $this->dataModel->name;




        $this->view->registerJs("var current_url = '" . Url::to($this->dataModel->getUrl()) . "';", yii\web\View::POS_HEAD, 'current_url');



        if (Yii::$app->request->get('sort') == 'price' || Yii::$app->request->get('sort') == '-price') {
            $this->query->aggregatePriceSelect((Yii::$app->request->get('sort') == 'price') ? SORT_ASC : SORT_DESC);
        }

        $this->provider = new \panix\engine\data\ActiveDataProvider([
            'query' => $this->query,
            'id' => null,
            'pagination' => [
                'pageSize' => $this->per_page,
            ]
        ]);


        $this->breadcrumbs[] = [
            'label' => Yii::t('shop/default', 'MANUFACTURER'),
            'url' => ['/manufacturer']
        ];
        $this->breadcrumbs[] = $this->pageName;
        $filterData = $this->getActiveFilters();




        $currentUrl[] = '/shop/manufacturer/view';
        $currentUrl['slug'] = $this->dataModel->slug;
        //  print_r($filterData);die;
        foreach ($filterData as $name => $filter) {
            if (isset($filter['name'])) { //attributes
                $currentUrl[$filter['name']] = [];
                if (isset($filter['items'])) {
                    $params = [];
                    foreach ($filter['items'] as $item) {
                        $params[] = $item['value'];
                    }
                    $currentUrl[$filter['name']] = implode(',', $params);
                }
            }
        }

        $this->currentUrl = Url::to($currentUrl);

        return $this->_render();

    }

    /**
     * @param $slug
     * @return mixed
     */
    protected function findModel($slug)
    {
        $this->dataModel = Manufacturer::find()
            ->where(['slug' => $slug])
            ->published()
            ->one();

        if ($this->dataModel !== null) {
            return $this->dataModel;
        } else {
            $this->error404('manufacturer not found');
        }
    }

}
