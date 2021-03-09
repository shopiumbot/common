<?php

namespace core\components\controllers;


use core\components\ManagerLanguage;
use shopium\mod\admin\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;


/**
 * Class AdminController
 * @package panix\engine\controllers
 */
class AdminController extends CommonController
{

    public $buttons = [];
    public $layout = '@theme/views/layouts/main';
    public $dashboard = true;

    public function actionSetLanguage($lang)
    {

        $cookies = Yii::$app->response->cookies;
        //$session = Yii::$app->session;

        /** @var ManagerLanguage $lm */
        $lm = Yii::$app->languageManager;
        if (in_array($lang, $lm->getCodes())) {
            Yii::$app->language = $lang;
            //$session->set('language', $lang);


            $cookie = new \yii\web\Cookie([
                'name' => 'language',
                'value' => Yii::$app->language,
                'expire' => time() + 86400 * 365,
            ]);
            $cookies->add($cookie);

            Yii::$app->session->setFlash('success', Yii::t('default', 'LANGUAGE_SET', mb_strtolower($lm->getByCode(Yii::$app->language)->name)));

            if (Yii::$app->request->get('redirect')) {
                return $this->redirect(Yii::$app->request->get('redirect'));
            }
        }
        return $this->redirect(['/']);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        // 'allow' => false,
                        'roles' => ['?'],
                        'denyCallback' => function ($rule, $action) {
                            return $this->redirect(['login']);
                            //  throw new \Exception('У вас нет доступа к этой странице');
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            return Yii::$app->user->id === (int)Yii::$app->params['client_id'];
                        },

                    ],

                ],
                'denyCallback' => function ($rule, $action) {
                    throw new HttpException(404, Yii::t('app/error', 404));
                }
            ]
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
            return $this->redirect(['/admin/admin/default/index']);

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login((int)Yii::$app->settings->get('user', 'login_duration') * 86400)) {
            return $this->goBack(['/admin/admin/default/index']);
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

    public function beforeAction2($action)
    {

        if (Yii::$app->user->isGuest && get_class($this) !== 'shopium\mod\admin\controllers\AuthController') {
            die('z');
            return $this->redirect(Yii::$app->user->loginUrl);
        }
        return parent::beforeAction($action);
    }

    public function beforeAction($action)
    {

        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $this->jsMessages = [
                'error' => [
                    '404' => Yii::t('app/error', '404')
                ],
                'cancel' => Yii::t('app/default', 'CANCEL'),
                'send' => Yii::t('app/default', 'SEND'),
                'delete' => Yii::t('app/default', 'DELETE'),
                'save' => Yii::t('app/default', 'SAVE'),
                'close' => Yii::t('app/default', 'CLOSE'),
                'ok' => Yii::t('app/default', 'OK'),
                'loading' => Yii::t('app/default', 'LOADING'),
            ];
            $this->view->registerJs('
            var common = window.common || {};
                        common.baseUrl = "' . Yii::$app->request->baseUrl . '";
            common.language = "' . Yii::$app->language . '";
            common.isDashboard = "' . $this->dashboard . '";
            common.message = ' . \yii\helpers\Json::encode($this->jsMessages) . ';', \yii\web\View::POS_HEAD, 'js-common');
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
        //$this->api = new \shopium\mod\telegram\components\Api(Yii::$app->user->token);
        //$bot = \shopium\mod\telegram\models\User::find()->where(['id'=>$this->api->getBotId()])->one();
        // if($bot){
        //     $this->botPhoto = $bot->getPhoto();
        // }

        parent::init();
    }


    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }


}
