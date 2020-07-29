<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193213_shop_product
 */

use core\modules\shop\models\Product;
use panix\engine\db\Migration;

class m180917_193213_shop_product extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function up()
    {


        //if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        //}

        $this->createTable(Product::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'custom_id'=> $this->integer()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'manufacturer_id' => $this->integer()->unsigned(),
            'main_category_id' => $this->integer()->unsigned(),
            'type_id' => $this->smallInteger()->unsigned(),
            'currency_id' => $this->smallInteger()->unsigned(),
            'weight_class_id' => $this->integer(),
            'length_class_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
            'price' => $this->money(10, 2),
            'unit' => $this->tinyInteger(1)->unsigned()->defaultValue(1),
            'max_price' => $this->money(10, 2),
            'label' => $this->tinyInteger(1)->null(),
            'sku' => $this->string(50),
            'weight' => $this->decimal(15, 4),
            'length' => $this->decimal(15, 4),
            'width' => $this->decimal(15, 4),
            'height' => $this->decimal(15, 4),
            'quantity' => $this->smallInteger(2)->unsigned()->defaultValue(1),
            'archive' => $this->boolean()->defaultValue(0),
            'availability' => $this->tinyInteger(1)->unsigned()->defaultValue(1),
            'auto_decrease_quantity' => $this->smallInteger(2)->unsigned()->defaultValue(0),
            'added_to_cart_count' => $this->integer()->defaultValue(0),
            'votes' => $this->integer()->unsigned()->defaultValue(0),
            'rating' => $this->integer()->unsigned()->defaultValue(0),
            'discount' => $this->string(5),
            'markup' => $this->string(50),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
            'ordern' => $this->integer()->unsigned(),
        ],$tableOptions);



        $this->createIndex('user_id', Product::tableName(), 'user_id');
        $this->createIndex('manufacturer_id', Product::tableName(), 'manufacturer_id');
        $this->createIndex('type_id', Product::tableName(), 'type_id');
        $this->createIndex('currency_id', Product::tableName(), 'currency_id');
        $this->createIndex('price', Product::tableName(), 'price');
        $this->createIndex('max_price', Product::tableName(), 'max_price');
        $this->createIndex('switch', Product::tableName(), 'switch');
        $this->createIndex('created_at', Product::tableName(), 'created_at');
        $this->createIndex('ordern', Product::tableName(), 'ordern');
        $this->createIndex('main_category_id', Product::tableName(), 'main_category_id');
        $this->createIndex('custom_id', Product::tableName(), 'custom_id');


        $this->loadColumns('grid-product', Product::class, ['image', 'name', 'price', 'created_at']);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(Product::tableName());
    }

}
