<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193704_shop_product_category_ref
 */
use yii\db\Schema;
use panix\engine\db\Migration;
use core\modules\shop\models\ProductCategoryRef;

class m180917_193704_shop_product_category_ref extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(ProductCategoryRef::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'product' => $this->integer()->notNull()->unsigned(),
            'category' => $this->integer()->notNull()->unsigned(),
            'is_main' => $this->boolean()->defaultValue(0),
            'switch' => $this->boolean()->defaultValue(1),
            'availability' => $this->tinyInteger(1)->defaultValue(0),
        ], $tableOptions);


        $this->createIndex('availability', ProductCategoryRef::tableName(), 'availability');
        $this->createIndex('product', ProductCategoryRef::tableName(), 'product');
        $this->createIndex('category', ProductCategoryRef::tableName(), 'category');
        $this->createIndex('switch', ProductCategoryRef::tableName(), 'switch');
        $this->createIndex('is_main', ProductCategoryRef::tableName(), 'is_main');
    }

    public function down()
    {
        $this->dropTable(ProductCategoryRef::tableName());
    }

}
