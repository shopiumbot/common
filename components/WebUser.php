<?php

namespace core\components;

use panix\engine\CMS;
use Yii;
use yii\db\Connection;
use yii\web\User;

/**
 * User component
 */
class WebUser extends User
{

    /**
     * @inheritdoc
     */
    public $identityClass = 'core\components\User';

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ["/user/login"];

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function getIsLoggedIn()
    {
        return !$this->getIsGuest();
    }

    /**
     * @param \app\modules\user\models\User $identity
     * @inheritdoc
     */
    public function afterLogin($identity, $cookieBased, $duration)
    {
        $identity->updateLoginMeta();
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "username")
    {
        $user = $this->getIdentity();
        return $user ? $user->getDisplayName($default) : $this->username;
    }

    public function getLanguage()
    {
        $user = $this->getIdentity();
        return $user ? $user->language : "";
    }
    public function getBotAdmins()
    {
        $user = $this->getIdentity();
        return $user ? $user->bot_admins : [];
    }

    public function getToken()
    {
        $user = $this->getIdentity();
        if ($user) {
            return $user->token;
        } else {
            $class = $this->identityClass;
            if (Yii::$app->request->get('webhook')) {
                $user = $class::findByHook(Yii::$app->request->get('webhook'));
            } elseif (Yii::$app->request->get('api_key')) {

                $user = $class::findIdentityByAccessToken(Yii::$app->request->get('api_key'));
            }
            $user = $class::findOne(Yii::$app->params['client_id']);
            return $user->token;
        }
    }

    public function getToken222()
    {

        $user = $this->getIdentity();
        if ($user) {
            return $user->token;
        } else {
            $class = $this->identityClass;
            if (Yii::$app->request->get('webhook')) {
                $user = $class::findByHook(Yii::$app->request->get('webhook'));
            } elseif (Yii::$app->request->get('api_key')) {

                $user = $class::findIdentityByAccessToken(Yii::$app->request->get('api_key'));
            }
            return $class::findOne(Yii::$app->params['client_id'])->token;
        }
    }

    public function getClientDb()
    {
        $user = $this->getIdentity();
        if ($user) {

            return Yii::$app->clientDb;
            //return $user->getClientDb();
        } else {
            return Yii::$app->clientDb;
/*
            if (Yii::$app->request->get('webhook') || Yii::$app->request->get('api_key')) {
                return Yii::$app->cache->getOrSet('client_db', function () {
                    $class = $this->identityClass;
                    if (Yii::$app->request->get('webhook')) {
                        $user = $class::findByHook(Yii::$app->request->get('webhook'));
                    } elseif (Yii::$app->request->get('api_key')) {

                        $user = $class::findIdentityByAccessToken(Yii::$app->request->get('api_key'));
                    }

                    return new Connection([
                        'dsn' => strtr('mysql:host={db_host};dbname={db_name}', [
                            '{db_name}' => $user->db_name,
                            '{db_host}' => $user->db_host,
                        ]),
                        'username' => $user->db_user,
                        'password' => $user->db_password,
                        'charset' => 'utf8',
                        'tablePrefix' => 'client_',
                        'serverStatusCache' => YII_DEBUG ? 0 : 3600,
                        'schemaCacheDuration' => YII_DEBUG ? 0 : 3600 * 24,
                        'queryCacheDuration' => YII_DEBUG ? 0 : 3600 * 24 * 7,
                        'enableSchemaCache' => true,
                        'schemaCache' => 'cache'
                    ]);
                });


            }*/
        }

    }

    public function getWebhook()
    {
        $user = $this->getIdentity();
        return $user ? $user->webhook : Yii::$app->request->get('webhook');
    }
    public function getWebhookUrl()
    {
        return 'https://shopiumbot.com/user/webhook/'.$this->getWebhook();
    }

    public function getDb_name()
    {
        $user = $this->getIdentity();
        return $user ? $user->db_name : null;
    }

    public function getDb_host()
    {
        $user = $this->getIdentity();
        return $user ? $user->db_host : null;
    }
    public function getDb_user()
    {
        $user = $this->getIdentity();
        return $user ? $user->db_user : null;
    }
    public function getDb_password()
    {
        $user = $this->getIdentity();
        return $user ? $user->db_password : null;
    }

    public function getEmail()
    {
        $user = $this->getIdentity();
        return $user ? $user->email : "";
    }

    public function getTimezone()
    {
        $user = $this->getIdentity();
        //return $user ? $user->timezone : NULL;
    }


    public function getPhone()
    {
        $user = $this->getIdentity();
        return $user ? $user->phone : "";
    }

    public function getBanTime()
    {
        $user = $this->getIdentity();
        return $user ? $user->ban_time : false;
    }

    public function getBanReason()
    {
        $user = $this->getIdentity();
        return $user ? $user->ban_reason : false;
    }

    public function getUsername()
    {
        $user = $this->getIdentity();
        return $user ? $user->username : "";
    }

    /**
     * @param $size
     * @param array $options
     * @return string
     */
    public function getGuestAvatarUrl($size, $options = [])
    {
        return CMS::processImage($size, 'guest.png', '@uploads/users/avatars', $options);
    }

    /**
     * Check if user can do $permissionName.
     * If "authManager" component is set, this will simply use the default functionality.
     * Otherwise, it will use our custom permission system
     *
     * @param string $permissionName
     * @param array $params
     * @param bool $allowCaching
     * @return bool

    public function can($permissionName, $params = [], $allowCaching = true)
     * {
     * // check for auth manager to call parent
     * $auth = Yii::$app->getAuthManager();
     * if ($auth) {
     * return parent::can($permissionName, $params, $allowCaching);
     * }
     *
     * // otherwise use our own custom permission (via the role table)
     *
     * $user = $this->getIdentity();
     * return $user ? $user->can($permissionName) : false;
     * }*/

}
