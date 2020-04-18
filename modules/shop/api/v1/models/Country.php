<?php

namespace core\modules\shop\api\v1\models;

use \yii\db\ActiveRecord;

/**
 * Class Country
 * @package core\modules\shop\api\v1\models
 */
class Country extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Define rules for validation
     */
    public function rules()
    {
        return [
            [['id', 'email'], 'required']
        ];
    }
}
