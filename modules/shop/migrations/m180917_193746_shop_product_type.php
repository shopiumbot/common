<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193746_shop_product_type
 */

use panix\engine\db\Migration;
use core\modules\shop\models\ProductType;

class m180917_193746_shop_product_type extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(ProductType::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->null(),
            'categories_preset' => $this->text()->null()->defaultValue(null),
            'main_category' => $this->integer(11)->null()->defaultValue(0),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable(ProductType::tableName());
    }

}
