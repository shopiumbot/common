<?php

namespace core\modules\shop\controllers\admin;

use Yii;
use core\modules\shop\models\AttributeGroup;
use core\modules\shop\models\search\AttributeGroupSearch;
use core\components\controllers\AdminController;

class AttributeGroupController extends AdminController {

    public function actions() {
        return [
            'sortable' => [
                'class' => \panix\engine\grid\sortable\Action::class,
                'modelClass' => AttributeGroup::class,
            ],
        ];
    }



    public function actionIndex() {
        $this->pageName = Yii::t('shop/admin', 'ATTRIBUTE_GROUP');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('shop/AttributeGroup', 'CREATE_GROUP'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        $this->breadcrumbs[] = [
            'label' => Yii::t('shop/default', 'MODULE_NAME'),
            'url' => ['/admin/shop']
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('shop/admin', 'ATTRIBUTES'),
            'url' => ['/admin/shop/attribute']
        ];
        $this->breadcrumbs[] = $this->pageName;

        $searchModel = new AttributeGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false) {

        $model = AttributeGroup::findModel($id);

        $this->pageName = Yii::t('shop/admin', 'ATTRIBUTE_GROUP');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('shop/AttributeGroup', 'CREATE_GROUP'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label' => $this->module->info['label'],
            'url' => $this->module->info['url'],
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('shop/admin', 'ATTRIBUTES'),
            'url' => ['/admin/shop/attribute']
        ];
        $this->breadcrumbs[] = [
            'label' => $this->pageName,
            'url' => ['index']
        ];

        $this->breadcrumbs[] = Yii::t('app/default', 'UPDATE');


        $isNew = $model->isNewRecord;
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->save();

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id) {
        $model = new AttributeGroup;
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            $this->error404();
        }
    }

}
