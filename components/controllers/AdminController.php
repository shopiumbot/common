<?php

namespace core\components\controllers;


use Yii;
use yii\filters\AccessControl;


/**
 * Class AdminController
 * @package panix\engine\controllers
 */
class AdminController extends CommonController
{

    public $buttons = [];
    public $layout = '@theme/views/layouts/main';
    public $dashboard = true;
    public $api;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                //'allowActions' => [
                // 'index',
                // The actions listed here will be allowed to everyone including guests.
                // ]

                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->id === Yii::$app->params['client_id'];
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * @param boolean $isNewRecord
     * @param array $post
     * @param array $action
     * @return \yii\web\Response
     */
    public function redirectPage($isNewRecord, $post, $action = ['index'])
    {

        Yii::$app->session->setFlash('success', Yii::t('app/default', ($isNewRecord) ? 'SUCCESS_CREATE' : 'SUCCESS_UPDATE'));
        $redirect = (isset($post['redirect'])) ? $post['redirect'] : Yii::$app->request->url;

        if ($isNewRecord) {
            return $this->redirect($action);
        }
        if (!Yii::$app->request->isAjax)
            return Yii::$app->response->redirect($redirect);
    }

    public function getAssetUrl()
    {
        $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@theme/assets"));
        return $assetsPaths[1];
    }


    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest && get_class($this) !== 'shopium\mod\admin\controllers\AuthController') {
            return Yii::$app->response->redirect(['/admin/auth']);
        }
        return parent::beforeAction($action);
    }


    /**
     * @inheritdoc
     */
    public function init()
    {

        Yii::setAlias('@theme', Yii::getAlias("@core/web/themes/dashboard"));
        Yii::setAlias('@web_theme', Yii::getAlias("@app/web/themes/" . Yii::$app->settings->get('app', 'theme')));
        $this->api = new \shopium\mod\telegram\components\Api(Yii::$app->user->token);
        parent::init();
    }


    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }


}
