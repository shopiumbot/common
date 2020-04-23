<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193421_shop_attribute
 */

use Yii;
use panix\engine\CMS;
use panix\engine\db\Migration;
use core\modules\shop\models\Attribute;
use core\modules\shop\models\translate\AttributeTranslate;

class m180917_193421_shop_attribute extends Migration
{

    public function up()
    {

        $this->createTable(Attribute::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'type' => $this->string(10)->notNull(),
            'title' => $this->string(255)->notNull(),
            'abbreviation' => $this->string(25)->null(),
            'hint' => $this->text()->null(),
            'select_many' => $this->boolean()->defaultValue(0),
            'required' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'sort' => $this->tinyInteger(1)->defaultValue(NULL),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
            'ordern' => $this->integer(11)->unsigned(),
        ]);
        $this->createIndex('name', Attribute::tableName(), 'name');
        $this->createIndex('ordern', Attribute::tableName(), 'ordern');


    }

    public function down()
    {
        $this->dropTable(Attribute::tableName());
    }

}
