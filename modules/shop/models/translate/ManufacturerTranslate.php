<?php

namespace core\modules\shop\models\translate;

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
class ManufacturerTranslate extends ActiveRecord implements InterfaceTranslateModel
{
    public static $translationAttributes = ['name'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop__manufacturer_translate}}';
    }

}
