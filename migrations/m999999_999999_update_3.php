<?php

use panix\engine\db\Migration;
use panix\engine\components\Settings;

class m999999_999999_update_3 extends Migration
{


    public function safeUp()
    {
        $this->dropColumn(\core\modules\shop\models\Product::tableName(), 'use_in_variants');
        $this->addColumn(\core\modules\shop\models\Attribute::tableName(), 'use_in_variants', $this->tinyInteger(1)->defaultValue(0));
    }


}
