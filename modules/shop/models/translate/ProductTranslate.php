<?php

namespace app\modules\shop\models\translate;

use yii\db\ActiveRecord;

/**
 * Class to access product translations
 *
 * @property int $id
 * @property int $object_id
 * @property int $language_id
 * @property string $name
 * @property string $short_description
 * @property string $full_description
 */
class ProductTranslate extends ActiveRecord
{
    public static $translationAttributes = ['name', 'full_description'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop__product_translate}}';
    }

}
