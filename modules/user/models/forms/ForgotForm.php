<?php

namespace core\modules\user\models\forms;

use core\modules\user\models\User;
use core\modules\user\models\UserKey;
use Yii;
use yii\base\Model;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

/**
 * Forgot password form
 */
class ForgotForm extends Model {

    /**
     * @var string Username and/or email
     */
    public $email;

    /**
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            ["email", "required"],
            ["email", "email"],
            ["email", "validateEmail"],
            ["email", "filter", "filter" => "trim"],
        ];
    }

    /**
     * Validate email exists and set user property
     */
    public function validateEmail() {
        // check for valid user
        $this->_user = $this->getUser();
        if (!$this->_user) {
            $this->addError("email", Yii::t("user/default", "Email not found"));
        }
    }

    /**
     * Get user based on email
     *
     */
    public function getUser() {
        // get and store user
        if ($this->_user === false) {

            $this->_user = User::findOne(["email" => $this->email]);
        }
        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            "email" => Yii::t("user/default", "Email"),
        ];
    }

    /**
     * Send forgot email
     *
     * @return bool
     */
    public function sendForgotEmail() {
        /** @var Mailer $mailer */
        /** @var Message $message */
        // validate
        if ($this->validate()) {

            // get user
            $user = $this->getUser();

            // calculate expireTime (converting via strtotime)
            $expireTime = Yii::$app->getModule("user")->resetKeyExpiration;
            $expireTime = $expireTime !== null ? date("Y-m-d H:i:s", strtotime("+" . $expireTime)) : null;

            // create userKey
            $userKey = new UserKey();
            $userKey = $userKey::generate($user->id, $userKey::TYPE_PASSWORD_RESET, $expireTime);

            // modify view path to module views
            $mailer = Yii::$app->mailer;
            $oldViewPath = $mailer->viewPath;
            $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

            // send email
            $subject = Yii::t("user/default", "FORGOT");
            $message = $mailer->compose('forgotPassword', compact("subject", "user", "userKey"))
                    ->setTo($user->email)
                    ->setSubject($subject);
            
           /* $message = $mailer->compose()
                    ->setHtmlBody(\panix\engine\CMS::textReplace(Yii::$app->settings->get('user','mail_forgot'),[
                        
                    ]))
                    ->setTo($user->email)
                    ->setSubject($subject);*/

            // check for messageConfig before sending (for backwards-compatible purposes)
            if (empty($mailer->messageConfig["from"])) {
                $message->setFrom('asd@dsa.ru');
            }
            $result = $message->send();

            // restore view path and return result
            $mailer->viewPath = $oldViewPath;
            return $result;
        }

        return false;
    }

}
