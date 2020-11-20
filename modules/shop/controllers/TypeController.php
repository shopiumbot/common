<?php

namespace core\modules\shop\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use core\components\controllers\AdminController;
use core\modules\shop\models\ProductType;
use core\modules\shop\models\search\ProductTypeSearch;
use core\modules\shop\models\Attribute;

/**
 * Class TypeController
 * @package core\modules\shop\controllers\admin
 */
class TypeController extends AdminController
{

    public $icon = 'icon-t';

    public function actions()
    {
        return [
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => ProductType::class,
            ],
        ];
    }

    /**
     * Display types list
     */
    public function actionIndex()
    {
        $this->pageName = Yii::t('shop/admin', 'TYPE_PRODUCTS');
        $this->view->params['breadcrumbs'][] = $this->pageName;
        // $this->topButtons = array(array('label' => Yii::t('shop/admin', 'Создать тип'),
        //         'url' => $this->createUrl('create'), 'htmlOptions' => array('class' => 'btn btn-success')));


        $this->buttons = [
            [
                'label' => Yii::t('app/default', 'CREATE'),
                'url' => ['/admin/shop/type/create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $searchModel = new ProductTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Update product type
     * @param bool $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id = false)
    {


        $model = ProductType::findModel($id, Yii::t('shop/admin', 'NO_FOUND_TYPE_PRODUCT'));

        $this->pageName = ($model->isNewRecord) ? Yii::t('shop/admin', 'TYPE_CREATE') :
            Yii::t('shop/admin', 'TYPE_UPDATE');


        $this->view->params['breadcrumbs'][] = [
            'label' => $this->module->info['label'],
            'url' => $this->module->info['url'],
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('shop/admin', 'TYPE_PRODUCTS'),
            'url' => ['/admin/shop/type'],
        ];
        $this->view->params['breadcrumbs'][] = $this->pageName;

        \core\modules\shop\bundles\admin\ProductTypeAsset::register($this->view);

        $post = Yii::$app->request->post();

        $isNew = $model->isNewRecord;
        if ($model->load($post)) {

            if (Yii::$app->request->post('categories')) {
                $model->categories_preset = serialize(Yii::$app->request->post('categories'));
                $model->main_category = Yii::$app->request->post('main_category');
            } else {
                //return defaults when all checkboxes were checked off
                $model->categories_preset = null;
                $model->main_category = 0;
            }

            if ($model->validate()) {
                $model->save();

                // Set type attributes
                $model->useAttributes(Yii::$app->request->post('attributes', []));

                return $this->redirectPage($isNew, $post);
            }
        }

        $allAttributes = Attribute::find()
            ->where(['NOT IN', 'id', ArrayHelper::map($model->attributeRelation, 'attribute_id', 'attribute_id')])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'attributes' => $allAttributes,
        ]);
    }

    /**
     * Delete type
     * @param array $id
     * @return \yii\web\Response
     */
    public function actionDelete($id = [])
    {
        if (Yii::$app->request->isPost) {
            $model = ProductType::model()->findAllByPk($_REQUEST['id']);

            if (!empty($model)) {
                foreach ($model as $m) {
                    if ($m->productsCount > 0) {
                        $this->error404(Yii::t('shop/admin', 'ERROR_DEL_TYPE_PRODUCT'));
                    } else {
                        $m->delete();
                    }
                }
            }

            if (!Yii::$app->request->isAjax)
                return $this->redirect('index');
        }
    }

}
