<?php

namespace core\modules\menu\models;


use panix\engine\emoji\Emoji;
use panix\engine\emoji\EmojiAsset;
use Yii;
use core\components\ActiveRecord;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property integer $created_at
 * @property integer $updated_at
 */
class Menu extends ActiveRecord
{

    const route = '/admin/menu/default';
    const MODULE_ID = 'menu';
    public $translationClass = MenuTranslate::class;
    public $disallow_delete = [1, 2, 3, 4, 5, 6];
    public $disallow_switch = [1];

    public static function find()
    {
        return new MenuQuery(get_called_class());
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [];
        if (!in_array($this->id, $this->disallow_delete)) {
            $rules[] = [['content'], 'string', 'max' => 4096];
            $rules[] = [['content'], 'required'];
        }else{
            $rules[] = [['callback'], 'required'];
        }


        $rules[] = [['name'], 'required'];
        $rules[] = [['name', 'callback'], 'string', 'max' => 255];
        $rules[] = [['name', 'callback'], 'trim'];


        //[['date_update'], 'date', 'format' => 'php:U']
        /// [['date_update'], 'date','format'=>'php:U', 'timestampAttribute' => 'date_update','skipOnEmpty'=>  true],
//[['date_update','date_create'], 'filter','filter'=>'strtotime'],
        return $rules;
    }

    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }


}
