<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193650_shop_product_attribute_eav
 */

use core\modules\shop\models\Product;
use panix\engine\db\Migration;
use core\modules\shop\models\ProductAttributesEav;
use core\modules\shop\models\Attribute;

class m180917_193650_shop_product_attribute_eav extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(ProductAttributesEav::tableName(), [
            'entity' => $this->integer()->unsigned(),
            'attribute' => $this->string(255)->null(),
            'value' => $this->text(),
        ],$tableOptions);

        $this->createIndex('entity', ProductAttributesEav::tableName(), 'entity');
        $this->createIndex('attribute', ProductAttributesEav::tableName(), 'attribute');
		
        if ($this->db->driverName != "sqlite") {
            $this->addForeignKey('{{%fk_product_attribute_eav_attribute}}', ProductAttributesEav::tableName(), 'attribute', Attribute::tableName(), 'name', "CASCADE", "CASCADE");
            $this->addForeignKey('{{%fk_product_attribute_eav_entity}}', ProductAttributesEav::tableName(), 'entity', Product::tableName(), 'id', "CASCADE", "CASCADE");
        }
    }

    public function down()
    {
        $this->dropTable(ProductAttributesEav::tableName());
    }

}
