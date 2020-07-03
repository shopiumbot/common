<?php

namespace core\modules\user\controllers;


use core\modules\user\models\forms\ChangePasswordForm;
use core\modules\user\models\Payments;
use panix\engine\CMS;
use Yii;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use core\modules\user\models\User;
use core\components\controllers\AdminController;
use panix\engine\bootstrap\ActiveForm;
use core\modules\user\models\forms\ForgotForm;
use core\modules\user\models\forms\ResendForm;


/**
 * AdminController implements the CRUD actions for User model.
 */
class DefaultController extends AdminController
{

    public $icon = 'users';

    /**
     * List all User models
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $user = User::findModel(Yii::$app->user->id);

        //  if ($user->isNewRecord) {
        //  $user->setScenario('admin');
        // }

        $this->pageName = Yii::t('user/default', 'MODULE_NAME');

        $this->breadcrumbs = [
            ['label' => $this->pageName, 'url' => ['index']],
            Yii::t('app/default', 'UPDATE')
        ];


        /*$this->buttons = [
            [
                'label' => Yii::t('user/default', 'Сбросить пароль и отправить на E-mail'),
                'url' => ['reset-password', 'id' => $user->id],
                'options' => ['class' => 'btn btn-success']
            ]
        ];*/
        $loadedPost = $user->load(Yii::$app->request->post());

        $isNew = $user->isNewRecord;
        $post = Yii::$app->request->post();
        if ($loadedPost && $user->validate()) {

            $user->save(false);
            return $this->redirectPage($isNew, $post);
        }


        $changePasswordForm = new ChangePasswordForm();
        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            $changePasswordForm->getUser()->setScenario("reset");
            $changePasswordForm->getUser()->attributes = $changePasswordForm->attributes;
            $changePasswordForm->getUser()->save(false);


            Yii::$app->session->setFlash("success", Yii::t("user/default", "Пароль успешно изменен"));
            return $this->refresh();
        }


        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user, $changePasswordForm);
        }
        // render
        return $this->render('index', ['model' => $user, 'changePasswordForm' => $changePasswordForm]);
    }


    public function actionResetPassword($id)
    {
        /** @var User $user */
        $user = User::findModel($id);
        $model = new ForgotForm();
        $this->pageName = Yii::t('user/default', 'FORGOT');
        if ($model->load(['ForgotForm' => ['email' => $user->email]]) && $model->sendForgotEmail()) {
            Yii::$app->session->setFlash("success", Yii::t("user/default", "FORGOT_SEND_SUCCESS"));
        }
        return $this->redirect(['update', 'id' => $user->id]);
    }

    public function actionSendActive($id)
    {
        /** @var User $user */
        $user = User::findModel($id);
        /** @var ResendForm $model */
        $model = Yii::$app->getModule("user")->model("ResendForm");

        if ($model->load(['ResendForm' => ['email' => $user->email]]) && $model->sendEmail()) {
            Yii::$app->session->setFlash("success", Yii::t("user/default", "CONFIRM_EMAIL_RESENT"));
        }
        return $this->redirect(['update', 'id' => $user->id]);
    }


    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionPaymentBalance($month)
    {
        if (!Yii::$app->user->isGuest) {
            $user = User::findOne(Yii::$app->user->id);
            $plan = Yii::$app->params['plan'][$user->plan_id];
            if (key_exists($month, $plan['prices'])) {
                if ($month >= 12) {
                    $text = 'Оплата тарифного плана "' . $plan['name'] . '" на 1 год';
                } else {
                    $text = 'Оплата тарифного плана "' . $plan['name'] . '" на 1 месяц';
                }
                $extend = $user->extendTariff($month, $text);
                if ($extend) {
                    Yii::$app->session->setFlash('success-payment', 'Вы успешно продлили тарифный план');
                } else {
                    Yii::$app->session->setFlash('error-payment', 'У Вас не достаточно средств');
                }
            } else {
                Yii::$app->session->setFlash('error-payment', 'Ошибка');
            }
            return $this->redirect(['/admin']);
        }
    }

    public function actionPaymentSuccess()
    {
        $request = Yii::$app->request;
        Yii::info('server');

        return true;
    }

    public function actionPaymentResult()
    {

        $request = Yii::$app->request;


        $liqPayConfig = Yii::$app->params['payment']['liqpay'];
        if ($request->post('data')) {

            $data = json_decode(base64_decode($request->post('data')));


            Yii::info(json_encode($data));
            list($gen, $user_id, $month) = explode('-', $data->order_id);


            $user = User::findOne((int)$user_id);


            if ($user === false) {
                throw new NotFoundHttpException('user not found');
            }


            // Create and check signature.
            $sign = base64_encode(sha1($liqPayConfig['private_key'] . $request->post('data') . $liqPayConfig['private_key'], 1));

            // If ok make order paid.
            if ($sign !== $request->post('signature')) {
                Yii::info('signature error');
                throw new NotFoundHttpException('signature error');
            }


            if ($data->status == 'success') {

                $payment = new Payments();
                $payment->system = 'liqpay';
                $payment->name = 'Пополнение баланса';
                $payment->type = 'balance';
                $payment->money = (Yii::$app->params['plan'][$user->plan_id]['prices'][$month] * $month);
                $payment->data = json_encode($data);
                $payment->status = $data->status;
                $payment->save(false);


                $user->money += (Yii::$app->params['plan'][$user->plan_id]['prices'][$month] * $month);
                $user->save(false);

                $extend = $user->extendTariff($month, $data->description);
                if ($extend) {
                    Yii::$app->session->setFlash('success-payment', 'Вы успешно продлили тарифный план');
                }

                return $this->redirect(['/admin']);
                // }

            } elseif ($data->status == 'failure') {


                $payment = new Payments();
                $payment->system = 'liqpay';
                $payment->name = 'Пополнение баланса';
                $payment->type = 'balance';
                $payment->money = (Yii::$app->params['plan'][$user->plan_id]['prices'][$month] * $month);
                $payment->data = json_encode($data);
                $payment->status = $data->status;
                $payment->save(false);


                Yii::$app->session->setFlash('error-payment', 'Платеж отменен');
                return $this->redirect(['/admin']);
            }
        } else {
            Yii::info('POST data - Not enabled');
            throw new ForbiddenHttpException('POST data - Not enabled');
        }


        return $user;
    }
}
