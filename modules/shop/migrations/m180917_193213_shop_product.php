<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193213_shop_product
 */

use core\modules\shop\models\Product;
use core\modules\shop\models\translate\ProductTranslate;
use panix\engine\db\Migration;

class m180917_193213_shop_product extends Migration
{
    public $settingsForm = 'core\modules\shop\models\forms\SettingsForm';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(Product::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'manufacturer_id' => $this->integer()->unsigned(),
            'category_id' => $this->integer()->unsigned(),
            'main_category_id' => $this->integer()->unsigned(),
            'type_id' => $this->smallInteger()->unsigned(),
            'supplier_id' => $this->integer()->unsigned(),
            'currency_id' => $this->smallInteger()->unsigned(),
            'weight_class_id' => $this->integer(),
            'length_class_id' => $this->integer(),
            'use_configurations' => $this->boolean()->defaultValue(0),
            'name' => $this->string(255)->notNull(),
            'short_description' => $this->text()->null(),
            'full_description' => $this->text()->null(),
            'price' => $this->money(10, 2),
            'unit' => $this->tinyInteger(1)->unsigned()->defaultValue(1),
            'max_price' => $this->money(10, 2),
            'price_purchase' => $this->money(10, 2)->comment('Цена закупки'),
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
            'views' => $this->integer()->unsigned()->defaultValue(0),
            'added_to_cart_count' => $this->integer()->defaultValue(0),
            'votes' => $this->integer()->unsigned()->defaultValue(0),
            'rating' => $this->integer()->unsigned()->defaultValue(0),
            'discount' => $this->string(5),
            'markup' => $this->string(50),
            'video' => $this->text(),
            'enable_comments' => $this->tinyInteger(1)->defaultValue(1)->unsigned(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
            'ordern' => $this->integer()->unsigned(),
        ]);



        $this->createIndex('user_id', Product::tableName(), 'user_id');
        $this->createIndex('manufacturer_id', Product::tableName(), 'manufacturer_id');
        $this->createIndex('category_id', Product::tableName(), 'category_id');
        $this->createIndex('type_id', Product::tableName(), 'type_id');
        $this->createIndex('supplier_id', Product::tableName(), 'supplier_id');
        $this->createIndex('currency_id', Product::tableName(), 'currency_id');
        $this->createIndex('price', Product::tableName(), 'price');
        $this->createIndex('max_price', Product::tableName(), 'max_price');
        $this->createIndex('switch', Product::tableName(), 'switch');
        $this->createIndex('created_at', Product::tableName(), 'created_at');
        $this->createIndex('views', Product::tableName(), 'views');
        $this->createIndex('ordern', Product::tableName(), 'ordern');
        $this->createIndex('main_category_id', Product::tableName(), 'main_category_id');

        $this->loadSettings();
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
