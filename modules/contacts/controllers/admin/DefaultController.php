<?php

namespace core\modules\contacts\controllers\admin;

use Yii;
use core\components\controllers\AdminController;
use core\modules\contacts\models\SettingsForm;

/**
 * Class DefaultController
 * @package core\modules\contacts\controllers\admin
 */
class DefaultController extends AdminController
{


    public function actionIndex()
    {
        $this->pageName = $this->module->info['label'];
        $this->breadcrumbs = [
            $this->pageName
        ];
        $model = new SettingsForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                Yii::$app->session->setFlash("success", Yii::t('app/default', 'SUCCESS_UPDATE'));
                return $this->refresh();
            }else{
               // print_r($model->errors);die;
            }

        }
        return $this->render('index', [
            'model' => $model
        ]);
    }


}
