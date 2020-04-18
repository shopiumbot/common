<?php

namespace app\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 * Class m190315_062942_shop_attribute_group
 */

use panix\engine\db\Migration;
use app\modules\shop\models\AttributeGroup;

class m190315_062942_shop_attribute_group extends Migration
{

    public function up()
    {
        $this->createTable(AttributeGroup::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'switch' => $this->boolean()->notNull()->defaultValue(null),
            'ordern' => $this->integer()->unsigned(),
        ]);


        $this->createIndex('switch', AttributeGroup::tableName(), 'switch');
        $this->createIndex('ordern', AttributeGroup::tableName(), 'ordern');

    }

    public function down()
    {
        $this->dropTable(AttributeGroup::tableName());
    }

}
