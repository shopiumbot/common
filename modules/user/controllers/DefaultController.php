<?php

namespace core\modules\user\controllers;


use core\modules\user\models\forms\ChangePasswordForm;
use panix\engine\CMS;
use Yii;
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
            $user->setScenario('admin');
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

}
