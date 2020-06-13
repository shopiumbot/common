<?php

namespace core\modules\pages\controllers;


use Yii;
use core\modules\pages\models\Pages;
use core\modules\pages\models\PagesSearch;
use core\components\controllers\AdminController;
use yii\web\Response;
use yii\widgets\ActiveForm;


class DefaultController extends AdminController
{

    public function actions()
    {
        return [
            'sortable' => [
                'class' => 'panix\engine\grid\sortable\Action',
                'modelClass' => Pages::class,
            ],
            'switch' => [
                'class' => 'panix\engine\actions\SwitchAction',
                'modelClass' => Pages::class,
            ],
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Pages::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('pages/default', 'MODULE_NAME');
        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") ||  Yii::$app->user->can("/{$this->module->id}/{$this->id}/create")) {
            $this->buttons = [
                [
                    'icon' => 'add',
                    'label' => Yii::t('pages/default', 'CREATE_BTN'),
                    'url' => ['create'],
                    'options' => ['class' => 'btn btn-success']
                ]
            ];
        }
        $this->breadcrumbs = [
            $this->pageName
        ];

        $searchModel = new PagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {

        $model = Pages::findModel($id);
        $this->pageName = Yii::t('pages/default', 'CREATE_BTN');
        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") ||  Yii::$app->user->can("/{$this->module->id}/{$this->id}/create")) {
            $this->buttons = [
                [
                    'icon' => 'add',
                    'label' => Yii::t('pages/default', 'CREATE_BTN'),
                    'url' => ['create'],
                    'options' => ['class' => 'btn btn-success']
                ]
            ];
        }
        $this->breadcrumbs[] = [
            'label' => Yii::t('pages/default', 'MODULE_NAME'),
            'url' => ['index']
        ];
        $this->breadcrumbs[] = $this->pageName;
        $result = [];
        $result['success'] = false;
        $isNew = $model->isNewRecord;
        //$model->setScenario("admin");
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            //if (Yii::$app->request->isAjax) {
            //    Yii::$app->response->format = Response::FORMAT_JSON;
            //    return ActiveForm::validate($model);
            //}

            if ($model->validate()) {
                $model->save();
                $json['success']=false;
                if (Yii::$app->request->isAjax && Yii::$app->request->post('ajax')) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $json['success']=true;
                    $json['message']='Saved.';
                    return $json;
                }

                return $this->redirectPage($isNew, $post);
            } else {
                // print_r($model->getErrors());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }
}
