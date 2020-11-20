<?php

namespace core\modules\shop\controllers;

use core\components\models\Currencies;
use panix\engine\CMS;
use Yii;
use core\modules\shop\models\Currency;
use core\modules\shop\models\search\CurrencySearch;
use core\components\controllers\AdminController;
use yii\web\HttpException;

class CurrencyController extends AdminController
{


    public $icon = 'currencies';

    public function actions()
    {
        return [
            'sortable' => [
                'class' => \panix\engine\grid\sortable\Action::class,
                'modelClass' => Currency::class,
            ],
        ];
    }

    public function actionActive($id)
    {
        $model = $this->findModel($id);
        if ($model->switch == 0)
            $model->switch = 1;
        else
            $model->switch = 0;

        if (!$model->save()) {
            Yii::$app->session->setFlash("error", "Error saving");
        }
        $model->refresh();

        if (Yii::$app->request->isAjax) { // Render the index view
            return $this->actionIndex();
        } else
            return $this->redirect(['manufacturer/index']);
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('shop/admin', 'CURRENCY');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('shop/admin', 'CREATE_CURRENCY'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->view->params['breadcrumbs'][] = $this->pageName;

        $searchModel = new CurrencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {

        $model = Currency::findModel($id);
        $this->pageName = Yii::t('shop/admin', 'CURRENCY');
        $isNew = $model->isNewRecord;

        if ($isNew && isset(Yii::$app->request->get('Currency')['currency'])) {

            if (!in_array(Yii::$app->request->get('Currency')['currency'], $model::$currencies)) {
                throw new HttpException(404, 'Currency error');
            }
            $model->currency = Yii::$app->request->get('Currency')['currency'];
            //  $this->pageName .= ' '.$model->currency;

        }


        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('shop/admin', 'CREATE_CURRENCY'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => $this->pageName,
            'url' => ['index']
        ];

        $this->view->params['breadcrumbs'][] = Yii::t('app/default', 'UPDATE');

        $post = Yii::$app->request->post();

        if ($isNew && isset(Yii::$app->request->get('Currency')['currency'])) {
            $model->iso = $model->currency;
            $cur = Currencies::findOne(['iso' => $model->iso]);
            $model->name = $cur->name;
            $model->symbol = $cur->symbol;

        }
        if ($model->load($post)) {
            if ($model->validate()) {
                $model->save();
                Yii::$app->session->setFlash('success','OK');
                return $this->redirectPage($isNew, $post);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


}
