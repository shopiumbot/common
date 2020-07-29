<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_194311_shop_product_image
 */

use core\modules\shop\models\ProductImage;
use panix\engine\db\Migration;

class m180917_194311_shop_product_image extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(ProductImage::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'product_id' => $this->integer()->unsigned(),
            'filename' => $this->string(255)->notNull(),
            'is_main' => $this->boolean()->defaultValue(0)->notNull(),
            'created_at' => $this->integer(),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
            'ordern' => $this->integer()->unsigned(),
        ],$tableOptions);

        $this->createIndex('user_id', ProductImage::tableName(), 'user_id');
        $this->createIndex('ordern', ProductImage::tableName(), 'ordern');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(ProductImage::tableName());
    }

}
