<?php

namespace app\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193421_shop_attribute
 */

use Yii;
use panix\engine\CMS;
use panix\engine\db\Migration;
use app\modules\shop\models\Attribute;
use app\modules\shop\models\translate\AttributeTranslate;

class m180917_193421_shop_attribute extends Migration
{

    public function up()
    {

        $this->createTable(Attribute::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'group_id' => $this->integer(11)->null()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'type' => $this->string(10)->notNull(),
            'title' => $this->string(255)->notNull(),
            'abbreviation' => $this->string(25)->null(),
            'hint' => $this->text()->null(),
            'display_on_front' => $this->boolean()->defaultValue(1),
            'display_on_list' => $this->boolean()->defaultValue(0),
            'display_on_cart' => $this->boolean()->defaultValue(0),
            'display_on_grid' => $this->boolean()->defaultValue(0),
            'display_on_pdf' => $this->boolean()->defaultValue(0),
            'use_in_filter' => $this->boolean()->defaultValue(0),
            'use_in_variants' => $this->boolean()->defaultValue(0),
            'use_in_compare' => $this->boolean()->defaultValue(0),
            'select_many' => $this->boolean()->defaultValue(0),
            'required' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'sort' => $this->tinyInteger(1)->defaultValue(NULL),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
            'ordern' => $this->integer(11)->unsigned(),
        ]);



        $this->createIndex('name', Attribute::tableName(), 'name');
        $this->createIndex('use_in_filter', Attribute::tableName(), 'use_in_filter');
        $this->createIndex('display_on_list', Attribute::tableName(), 'display_on_list');
        $this->createIndex('display_on_front', Attribute::tableName(), 'display_on_front');
        $this->createIndex('display_on_cart', Attribute::tableName(), 'display_on_cart');
        $this->createIndex('display_on_grid', Attribute::tableName(), 'display_on_grid');
        $this->createIndex('display_on_pdf', Attribute::tableName(), 'display_on_pdf');
        $this->createIndex('ordern', Attribute::tableName(), 'ordern');
        $this->createIndex('use_in_variants', Attribute::tableName(), 'use_in_variants');
        $this->createIndex('use_in_compare', Attribute::tableName(), 'use_in_compare');



    }

    public function down()
    {
        $this->dropTable(Attribute::tableName());
    }

}
