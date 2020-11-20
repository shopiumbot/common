<?php

namespace core\modules\menu\controllers;


use Yii;
use core\modules\menu\models\Menu;
use core\modules\menu\models\MenuSearch;
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
                'modelClass' => Menu::class,
            ],
            'switch' => [
                'class' => 'panix\engine\actions\SwitchAction',
                'modelClass' => Menu::class,
            ],
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Menu::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('menu/default', 'MODULE_NAME');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('menu/default', 'CREATE_BTN'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        $this->view->params['breadcrumbs'] = [
            $this->pageName
        ];

        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {

        $model = Menu::findModel($id);
        $isNew = $model->isNewRecord;
        $this->pageName = ($isNew) ? Yii::t('menu/default', 'CREATE_BTN') : Yii::t('menu/default', 'UPDATE');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('menu/default', 'CREATE_BTN'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('menu/default', 'MODULE_NAME'),
            'url' => ['index']
        ];
        $this->view->params['breadcrumbs'][] = $this->pageName;
        $result = [];
        $result['success'] = false;

        //$model->setScenario("admin");
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            //if (Yii::$app->request->isAjax) {
            //    Yii::$app->response->format = Response::FORMAT_JSON;
            //    return ActiveForm::validate($model);
            //}

            if ($model->validate()) {
                $model->save();
                $json['success'] = false;
                if (Yii::$app->request->isAjax && Yii::$app->request->post('ajax')) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $json['success'] = true;
                    $json['message'] = 'Saved.';
                    return $json;
                }

                return $this->redirectPage($isNew, $post);
            } else {
                print_r($model->getErrors());
                die;
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
