<?php

namespace app\modules\shop\controllers;

use Yii;
use panix\engine\controllers\WebController;
use app\modules\shop\models\Product;


class DefaultController extends WebController {

    public function actionIndex(){
        return $this->render('index');
    }




}
