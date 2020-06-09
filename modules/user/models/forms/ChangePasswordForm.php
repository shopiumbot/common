<?php

namespace core\modules\user\models\forms;

use Yii;
use panix\engine\base\Model;

/**
 * Change password form
 */
class ChangePasswordForm extends Model
{

    protected $module = 'user';
    public $current_password;
    public $new_password;
    public $password_confirm;

    /**
     * @var \core\modules\user\models\User
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['new_password'], 'string', 'min' => 3],
            [['new_password'], 'filter', 'filter' => 'trim'],
            [['current_password'], 'validateCurrentPassword'],
            [['new_password', 'password_confirm', 'current_password'], 'required'],
            [['password_confirm'], 'compare', 'compareAttribute' => 'new_password', 'message' => self::t('ERROR_COMPARE_PASSWORDS')],
        ];
    }

    /**
     * Get user based on email
     *
     * @return \core\modules\user\models\User|null
     */
    public function getUser()
    {
        // get and store user
        if ($this->_user === false) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }

    public function validateCurrentPassword()
    {

        if (!$this->getUser()->verifyPassword($this->current_password)) {
            $this->addError("current_password", self::t('ERROR_CURRENT_PASSWORD'));
        }
    }

}