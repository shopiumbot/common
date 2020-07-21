<?php

namespace core\modules\user\models;

use panix\engine\CMS;
use Yii;
use core\components\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\helpers\Inflector;
use ReflectionClass;

/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 * @property integer $status
 * @property string $email
 * @property string $new_email
 * @property string $username
 * @property string $phone
 * @property string $password
 * @property string $auth_key
 * @property string $api_key
 * @property string $login_ip
 * @property string $login_time
 * @property string $login_user_agent
 * @property string $create_ip
 * @property string $create_time
 * @property string $update_time
 * @property string $language
 * @property int $expire
 * @property double $money
 * @property UserKey[] $userKeys
 * @property UserAuth[] $userAuths
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function getDb()
    {
        return Yii::$app->serverDb;
    }

    public $disallow_delete = [1];
    const MODULE_ID = 'user';
    const route = '/admin/user/default';
    /**
     * @var int Inactive status
     */
    const STATUS_INACTIVE = 0;

    /**
     * @var int Active status
     */
    const STATUS_ACTIVE = 1;

    /**
     * @var int Unconfirmed email status
     */
    const STATUS_UNCONFIRMED_EMAIL = 2;

    public $password_confirm;
    public $new_password;

    public function init()
    {

       //  $this->bot_admins = explode(',',$this->bot_admins);

        parent::init();

    }
   //  public function getBot_admins(){
    // return explode(',',$this->bot_admins);
    // }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%user}}";
    }

    public $currentPassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // set initial rules
        $rules = [

            [['money','expire'], 'required', 'on' => ['extendTariff']],


            [['money', 'expire'], 'string', 'max' => 255],
            // general email and username rules
            [['email', 'phone'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'email'],

            [['token', 'phone'], 'required'],

            ['token', 'validateBotToken'],
            ['new_password', 'string', 'min' => 3, 'on' => ['reset']],
            [['new_password'], 'required', 'on' => ['reset']],

            ['bot_admins', 'each', 'rule' => ['integer']],

            [['gender'], 'integer'],
            ['phone', 'panix\ext\telinput\PhoneInputValidator'],
        ];

        // add required rules for email/username depending on module properties
        $requireFields = ["requireEmail", "requireUsername"];
        foreach ($requireFields as $requireField) {
            if (Yii::$app->getModule("user")->$requireField) {
                $attribute = strtolower(substr($requireField, 7)); // "email" or "username"
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }

    public function validateBotToken($attribute)
    {


        Yii::$app->setComponents([
            'telegram2' => [
                'class' => 'shopium\mod\telegram\components\Telegram',
                'botToken' => $this->$attribute,
                //'botUsername' => $this->bot_name,
            ]
        ]);

        $response = Yii::$app->telegram2->getMe();
        $result = json_decode($response);

        if ($result->ok) {
            return true;
        } else {
            $this->addError($attribute, $result->description);
        }

    }

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            'register_fast' => ['email', 'phone'],
            'register' => ['email', 'password', 'password_confirm'],
            'reset' => ['new_password', 'password_confirm'],
            'extendTariff' => ['expire', 'money'],
            'admin' => ['bot_admins', 'token','email','phone'],
        ]);
    }

    /**
     * Validate current password (account page)
     */
    public function validateCurrentPassword()
    {
        if (!$this->verifyPassword($this->currentPassword)) {
            $this->addError("currentPassword", "Current password incorrect");
        }
    }


    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'new_password' => self::t('NEW_PASSWORD'),
            'password_confirm' => self::t('PASSWORD_CONFIRM'),
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserKeys()
    {
        return $this->hasMany(UserKey::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuths()
    {
        return $this->hasMany(UserAuth::class, ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(["api_key" => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Verify password
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }


    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

        // hash new password if set
        if ($this->password && $insert) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }

        if (in_array($this->scenario, ['reset', 'admin'])) {
            if ($this->new_password) {
                $this->password = Yii::$app->security->generatePasswordHash($this->new_password);
            }
        }


        if ($this->scenario == 'admin' && is_array($this->bot_admins)) {
            $this->bot_admins = implode(',', $this->bot_admins);
        }

        // ensure fields are null so they won't get set as empty string
        $nullAttributes = ["email", "username"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        return parent::beforeSave($insert);
    }


    public function getGenderList()
    {
        return [$this::t('FEMALE'), $this::t('MALE')];
    }

    /**
     * Set attributes for registration
     *
     * @param string $userIp
     * @param string $status
     * @return static
     */
    public function setRegisterAttributes($userIp, $status = null)
    {
        // set default attributes
        $attributes = [
            "create_ip" => $userIp,
            "auth_key" => Yii::$app->security->generateRandomString(),
            "api_key" => Yii::$app->security->generateRandomString(),
            "status" => static::STATUS_ACTIVE,
        ];

        // determine if we need to change status based on module properties
        $emailConfirmation = Yii::$app->getModule("user")->emailConfirmation;
        $requireEmail = Yii::$app->getModule("user")->requireEmail;
        $useEmail = Yii::$app->getModule("user")->useEmail;
        if ($status) {
            $attributes["status"] = $status;
        } elseif ($emailConfirmation && $requireEmail) {
            $attributes["status"] = static::STATUS_INACTIVE;
        } elseif ($emailConfirmation && $useEmail && $this->email) {
            $attributes["status"] = static::STATUS_UNCONFIRMED_EMAIL;
        }

        // set attributes and return
        $this->setAttributes($attributes, false);
        return $this;
    }

    /**
     * Check and prepare for email change
     *
     * @return bool True if user set a `new_email`
     */
    public function checkAndPrepEmailChange()
    {
        // check if user is removing email address (only if Module::$requireEmail = false)
        if (trim($this->email) === "") {
            return false;
        }

        // check for change in email
        if ($this->email != $this->getOldAttribute("email")) {

            // change status
            $this->status = static::STATUS_UNCONFIRMED_EMAIL;

            // set `new_email` attribute and restore old one
            $this->new_email = $this->email;
            $this->email = $this->getOldAttribute("email");

            return true;
        }

        return false;
    }

    /**
     * Update login info (ip and time)
     *
     * @return bool
     */
    public function updateLoginMeta()
    {
        // set data
        $this->login_ip = Yii::$app->getRequest()->getUserIP();
        $this->login_time = date("Y-m-d H:i:s");
        $this->login_user_agent = Yii::$app->getRequest()->getUserAgent();
        //$this->setScenario('disallow-timestamp');
        // save and return
        return $this->save(false, ["login_ip", "login_time", "login_user_agent"]);
    }

    /**
     * Confirm user email
     *
     * @return bool
     */
    public function confirm()
    {
        // update status
        $this->status = static::STATUS_ACTIVE;

        // update new_email if set
        if ($this->new_email) {
            $this->email = $this->new_email;
            $this->new_email = null;
        }

        // save and return
        return $this->save(false, ["email", "new_email", "status"]);
    }


    /**
     * Get display name for the user
     *
     * @var string $default
     * @return string|int
     */
    public function getDisplayName($default = "")
    {
        // define possible fields
        $possibleNames = [
            "username",
            "email",
            "id",
        ];

        // go through each and return if valid
        foreach ($possibleNames as $possibleName) {
            if (!empty($this->$possibleName)) {
                return $this->$possibleName;
            }
        }

        return $default;
    }

    /**
     * Send email confirmation to user
     *
     * @param UserKey $userKey
     * @return int
     */
    public function sendEmailConfirmation($userKey)
    {
        /** @var $mailer \yii\swiftmailer\Mailer */
        /** @var $message \yii\swiftmailer\Message */

        // modify view path to module views
        $mailer = Yii::$app->mailer;
        $oldViewPath = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        // send email
        $user = $this;
        $email = $user->new_email !== null ? $user->new_email : $user->email;
        $subject = Yii::t("user/default", "Email Confirmation");
        $message = $mailer->compose('confirmEmail', compact("subject", "user", "userKey"))
            ->setTo($email)
            ->setSubject($subject);

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;
        return $result;
    }

    /**
     * Get list of statuses for creating dropdowns
     *
     * @return array
     */
    public static function statusDropdown()
    {
        // get data if needed
        static $dropdown;
        if ($dropdown === null) {

            // create a reflection class to get constants
            $reflClass = new ReflectionClass(get_called_class());
            $constants = $reflClass->getConstants();

            // check for status constants (e.g., STATUS_ACTIVE)
            foreach ($constants as $constantName => $constantValue) {

                // add prettified name to dropdown
                if (strpos($constantName, "STATUS_") === 0) {
                    // $prettyName = str_replace("STATUS_", "", $constantName);
                    // $prettyName = Inflector::humanize(strtolower($prettyName));
                    $dropdown[$constantValue] = self::t($constantName);
                }
            }
        }

        return $dropdown;
    }


    public function extendTariff($month, $text)
    {
        $priceByMonth = Yii::$app->params['plan'][$this->plan_id]['prices'][$month] * $month;

        if ($this->money >= $priceByMonth) {
            $this->scenario = 'extendTariff';
            $this->expire = strtotime("+{$month} month",$this->expire);
            $this->money -= $priceByMonth;
            $this->trial = 0;
            if($this->save(false)){
               // echo date('Y-m-d',$this->expire);
               // die('save');
            }else{
               // die('no save');
            }


            $payment = new Payments();
            $payment->system = 'rate';
            $payment->name = $text;
            $payment->type = 'rate';
            $payment->money -= $priceByMonth;
            $payment->status = 'success';
            $payment->save(false);

            return true;
        }
        return false;
    }


}
