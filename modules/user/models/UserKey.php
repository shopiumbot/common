<?php

namespace core\modules\user\models;

use Yii;
use core\components\ActiveRecord;

/**
 * This is the model class for table "tbl_user_key".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $key
 * @property string $create_time
 * @property string $consume_time
 * @property string $expire_time
 * @property User $user
 */
class UserKey extends ActiveRecord
{
    const MODULE_ID = 'user';
    /**
     * @var int Key for email activations (for registrations)
     */
    const TYPE_EMAIL_ACTIVATE = 1;

    /**
     * @var int Key for email changes (=updating account page)
     */
    const TYPE_EMAIL_CHANGE = 2;

    /**
     * @var int Key for password resets
     */
    const TYPE_PASSWORD_RESET = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%user_key}}";
    }

    /**
     * No inputs are used for userKeys
     *
     * @inheritdoc
     */
    /*
      public function rules()
      {
      return [
      [['user_id', 'type', 'key'], 'required'],
      [['user_id', 'type'], 'integer'],
      [['create_time', 'consume_time', 'expire_time'], 'safe'],
      [['key'], 'string', 'max' => 255],
      [['key'], 'unique']
      ];
      }
     */

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user/default', 'ID'),
            'user_id' => Yii::t('user/default', 'User ID'),
            'type' => Yii::t('user/default', 'Type'),
            'key' => Yii::t('user/default', 'Key'),
            'create_time' => Yii::t('user/default', 'Create Time'),
            'consume_time' => Yii::t('user/default', 'Consume Time'),
            'expire_time' => Yii::t('user/default', 'Expire Time'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
                'attributes' => [
                    // set only create_time because there is no update_time
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Generate/reuse a userKey
     *
     * @param int $userId
     * @param int $type
     * @param string $expireTime
     * @return static
     */
    public static function generate($userId, $type, $expireTime = null)
    {
        // attempt to find existing record
        // otherwise create new
        $model = static::findActiveByUser($userId, $type);
        if (!$model) {
            $model = new static();
        }

        // set/update data
        $model->user_id = $userId;
        $model->type = $type;
        $model->created_at = date("Y-m-d H:i:s");
        $model->expire_time = $expireTime;
        $model->key = Yii::$app->security->generateRandomString();
        $model->save(false);
        return $model;
    }

    /**
     * Find an active userKey by userId
     *
     * @param int $userId
     * @param array|int $type
     * @return ActiveRecord
     */
    public static function findActiveByUser($userId, $type)
    {
        $now = date("Y-m-d H:i:s");
        return static::find()
            ->where([
                "user_id" => $userId,
                "type" => $type,
                "consume_time" => null,
            ])
            ->andWhere("([[expire_time]] >= '$now' or [[expire_time]] is NULL)")
            ->one();
    }

    /**
     * Find an active userKey by key
     *
     * @param string $key
     * @param array|int $type
     * @return ActiveRecord
     */
    public static function findActiveByKey($key, $type)
    {
        $now = date("Y-m-d H:i:s");
        return static::find()
            ->where([
                "key" => $key,
                "type" => $type,
                "consume_time" => null,
            ])
            ->andWhere("([[expire_time]] >= '$now' or [[expire_time]] is NULL)")
            //->createCommand()->rawSql;
            ->one();
    }

    /**
     * Consume userKey record
     *
     * @return static
     */
    public function consume()
    {
        $this->consume_time = date("Y-m-d H:i:s");
        $this->save(false);
        return $this;
    }

    /**
     * Expire userKey record
     *
     * @return static
     */
    public function expire()
    {
        $this->expire_time = date("Y-m-d H:i:s");
        $this->save(false);
        return $this;
    }

}
