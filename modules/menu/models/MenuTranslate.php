<?php

namespace core\modules\menu\models;

use yii\db\ActiveRecord;

/**
 * Class to access manufacturer translations
 *
 * @property int $id
 * @property int $object_id
 * @property int $language_id
 * @property string $name
 * @property string $description
 */
class MenuTranslate extends ActiveRecord
{
    public static $translationAttributes = ['name','content'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_translate}}';
    }

}
