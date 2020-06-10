<?php

namespace core\components\controllers;


use panix\engine\CMS;
use shopium\mod\admin\models\LoginForm;
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
    public $botPhoto;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['auth'],
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                        'denyCallback' => function ($rule, $action) {
                            throw new \Exception('У вас нет доступа к этой странице');
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
if(Yii::$app->user->isGuest){
    return false;
}

                            return Yii::$app->user->id === Yii::$app->params['client_id'];
                        },
                        'denyCallback' => function ($rule, $action) {
                            throw new \Exception('У вас нет доступа к этой странице');
                        }
                    ],
                ],
            ],
        ];
    }



    /**
     * Display admin panel login
     * @return string|\yii\web\Response
     */
    public function actionAuth()
    {
        $this->layout = '@theme/views/layouts/auth';
        $this->enableStatistic = false;
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['/admin']);

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login((int)Yii::$app->settings->get('user', 'login_duration') * 86400)) {
            return $this->goBack(['/admin']);
        }

        // render
        return $this->render('login', [
            'model' => $model,
        ]);
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

            return Yii::$app->response->redirect(['/admin/auth'])->send();
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
        $bot = \shopium\mod\telegram\models\User::find()->where(['id'=>$this->api->getBotId()])->one();
        if($bot){
            $this->botPhoto = $bot->getPhoto();
        }

        parent::init();
    }


    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }


}
