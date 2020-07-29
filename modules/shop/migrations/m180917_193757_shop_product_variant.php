<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193757_shop_product_variant
 */

use panix\engine\db\Migration;
use core\modules\shop\models\ProductVariant;

class m180917_193757_shop_product_variant extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(ProductVariant::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'attribute_id' => $this->integer(11)->null()->unsigned(),
            'option_id' => $this->integer(11)->null()->unsigned(),
            'product_id' => $this->integer(11)->null()->unsigned(),
            'price' => $this->float('10,2')->null(),
            'price_type' => $this->boolean()->null(),
            'sku' => $this->string(255)->null(),
        ],$tableOptions);

        $this->createIndex('attribute_id', ProductVariant::tableName(), 'attribute_id');
        $this->createIndex('option_id', ProductVariant::tableName(), 'option_id');
        $this->createIndex('product_id', ProductVariant::tableName(), 'product_id');
    }

    public function down()
    {
        $this->dropTable(ProductVariant::tableName());
    }

}
