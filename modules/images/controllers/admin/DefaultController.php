<?php

namespace core\modules\images\controllers\admin;

use Yii;
use panix\engine\controllers\WebController;
use core\modules\images\models\Image;

class DefaultController extends WebController {

    public function actions() {
        return [
            'sortable' => [
                'class' => \panix\engine\grid\sortable\Action::class,
                'modelClass' => Image::class,
                'successMessage' => Yii::t('shop/admin', 'SORT_IMAGE_SUCCESS_MESSAGE')
            ],
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Image::class,
            ],
        ];
    }

    public function actionGetImage($item = '', $m = '', $dirtyAlias) {

        $dotParts = explode('.', $dirtyAlias);
        if (!isset($dotParts[1])) {
            throw new \yii\web\HttpException(404, 'Image must have extension');
        }
        $dirtyAlias = $dotParts[0];

        $size = isset(explode('_', $dirtyAlias)[1]) ? explode('_', $dirtyAlias)[1] : false;
        $alias = isset(explode('_', $dirtyAlias)[0]) ? explode('_', $dirtyAlias)[0] : false;
        $image = \Yii::$app->getModule('images')->getImage($item, $m, $alias);


        if ($image && $image->getExtension() != $dotParts[1]) {
            throw new \yii\web\HttpException(404, 'Image not found (extension)');
        }

        if ($image) {
            header('Content-Type: ' . $image->getMimeType($size));
            echo $image->getContent($size);
        } else {
            throw new \yii\web\HttpException(404, 'There is no images');
        }
    }

    public function actionDelete() {
        $json = [];

        $entry = Image::find()
                ->where(['id' => Yii::$app->request->post('id')])
                ->all();
        if (!empty($entry)) {
            foreach ($entry as $page) {
                if (!in_array($page->primaryKey, $page->disallow_delete)) {

                    $page->delete();


                    if ($page->is_main) {
                        // Get first image and set it as main
                        $model = Image::find()
                                ->where(['product_id' => Yii::$app->request->post('product_id')])
                                ->one();

                        if ($model) {
                            $model->is_main = 1;
                            $model->save(false);
                        }
                    }
                }
            }
            $json = [
                'status' => 'success',
                'message' => Yii::t('app/default', 'SUCCESS_RECORD_DELETE')
            ];
        }


        echo \yii\helpers\Json::encode($json);
        Yii::$app->end();
    }

}
