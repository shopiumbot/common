<?php

namespace core\modules\images\controllers;

use panix\engine\CMS;
use Yii;
use yii\base\Response;
use yii\web\Controller;
use yii\web\HttpException;
use core\modules\images\models\Image;

class DefaultController extends Controller
{

    public function actions()
    {
        return [
            'sortable' => [
                'class' => 'panix\engine\grid\sortable\Action',
                'modelClass' => Image::class,
            ],
        ];
    }

    public function actionLogo()
    {

        Header('Content-type: text/xml');
        echo '<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin meet" fill="red">
<style type="text/css">
	.st2{fill:#F8E07A;}
	.st3{fill:#505759;}
</style>
	<polygon class="st2" points="18.4,5.7 36.7,16.3 36.7,13.8 18.4,3.2 18.4,3.2 0.1,13.7 0,13.7 0,16.3 	"/>
	<polygon class="st2" points="40.2,33 42.4,31.7 42.3,10.6 24,0 21.7,1.3 40.1,11.9 	"/>
	<polygon class="st2" points="7.8,21.9 5.6,23.1 5.5,44.3 23.8,54.9 23.8,55 26.1,53.7 7.8,43 	"/>
	<polygon class="st2" points="20.6,10.9 18.4,9.7 0.1,20.2 0,20.2 0,41.4 2.3,42.7 2.3,21.5 	"/>
	<polygon class="st2" points="45.7,12.1 45.8,33.3 27.5,43.9 29.6,45.2 47.9,34.7 48,34.6 48,13.5 48,13.4 	"/>
	<polygon class="st2" points="11.3,38.6 11.3,41.2 29.6,51.7 29.7,51.7 47.9,41.1 48,41.1 48,38.5 29.7,49.1 	"/>
	<polygon class="st3" points="23.9,33.3 19.5,36.1 19.5,39.1 23.9,36.2 28.4,39.1 28.4,36.1 		"/>
	<polygon class="st3" points="19.5,26.6 19.5,29.5 23.9,32.4 28.4,29.5 28.4,26.6 23.9,29.4 		"/>
	<polygon class="st3" points="30.7,16.8 25.9,19 25.9,24.3 28.4,25.9 28.4,20.6 33.2,18.4 		"/>
	<polygon class="st3" points="22,19 17.2,16.8 14.7,18.4 19.5,20.6 19.5,25.9 22,24.3 		"/>
	<polygon class="st3" points="11.3,30.9 16,33.1 18.6,31.5 13.8,29.3 13.8,24 11.3,25.6 		"/>
	<polygon class="st3" points="36.7,25.6 34.1,24 34.1,29.3 29.4,31.5 31.9,33.1 36.7,30.9 		"/>
</svg>';
        die;
    }

    public function actionGetFile($dirtyAlias)
    {

        $dotParts = explode('.', $dirtyAlias);
        if (!isset($dotParts[1])) {
            throw new HttpException(404, 'Image must have extension');
        }
        $dirtyAlias = $dotParts[0];

        $size = isset(explode('_', $dirtyAlias)[1]) ? explode('_', $dirtyAlias)[1] : false;
        $alias = isset(explode('_', $dirtyAlias)[0]) ? explode('_', $dirtyAlias)[0] : false;


        /** @var $image Image */
        $image = \Yii::$app->getModule('images')->getImage($alias);

        if ($image) {
            $response = Yii::$app->getResponse();
            $response->format = \yii\web\Response::FORMAT_RAW;
            $image->getContent($size);
            die;
        } else {
            throw new HttpException(404, 'There is no images');
        }
    }


    public function actionDelete()
    {
        $json = [];

        $entry = Image::find()
            ->where(['id' => Yii::$app->request->post('id')])
            ->all();
        if (!empty($entry)) {
            foreach ($entry as $page) {
                /** @var $page Image */
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
