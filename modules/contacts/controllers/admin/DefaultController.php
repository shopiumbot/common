<?php

namespace core\modules\contacts\controllers\admin;

use Yii;
use core\modules\contacts\models\Pages;
use core\modules\contacts\models\PagesSearch;
use panix\engine\controllers\AdminController;

/**
 * Class DefaultController
 * @package core\modules\contacts\controllers\admin
 */
class DefaultController extends AdminController
{


    public function actionIndex()
    {
        $this->pageName = Yii::t('contacts/default', 'MODULE_NAME');
        $this->breadcrumbs = [
            $this->pageName
        ];

        // $searchModel = new PagesSearch();
        // $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            //      'dataProvider' => $dataProvider,
            //     'searchModel' => $searchModel,
        ]);
    }


}
