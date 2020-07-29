<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193811_shop_related_product
 */

use panix\engine\db\Migration;
use core\modules\shop\models\RelatedProduct;

class m180917_193811_shop_related_product extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(RelatedProduct::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'product_id' => $this->integer(11)->null()->unsigned(),
            'related_id' => $this->integer(11)->null()->unsigned(),
        ],$tableOptions);

        $this->createIndex('product_id', RelatedProduct::tableName(), 'product_id');
        $this->createIndex('related_id', RelatedProduct::tableName(), 'related_id');
    }

    public function down()
    {
        $this->dropTable(RelatedProduct::tableName());
    }
}
