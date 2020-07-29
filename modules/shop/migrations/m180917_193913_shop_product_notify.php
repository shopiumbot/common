<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193913_shop_product_notify
 */
use core\modules\shop\models\ProductNotifications;
use panix\engine\db\Migration;

class m180917_193913_shop_product_notify extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(ProductNotifications::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->null(),
            'product_id' => $this->integer()->unsigned()->notNull(),
            'email' => $this->string(100),
        ],$tableOptions);


        // order product notify indexes
        $this->createIndex('product_id', ProductNotifications::tableName(), 'product_id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(ProductNotifications::tableName());

    }

}
