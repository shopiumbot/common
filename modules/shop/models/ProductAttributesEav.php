<?php

namespace core\modules\shop\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "ProductAttributesEav".
 */
class ProductAttributesEav extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%shop__product_attribute_eav}}';
    }

}
