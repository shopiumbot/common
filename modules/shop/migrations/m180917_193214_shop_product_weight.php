<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193214_shop_product_weight
 */

use Yii;
use panix\engine\db\Migration;

class m180917_193214_shop_product_weight extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable('{{%shop__product_weight}}', [
            'id' => $this->primaryKey()->unsigned(),
            'value' => $this->decimal(15,4),
            'title' => $this->string(32)->notNull(),
            'unit' => $this->string(4)->notNull(),
        ],$tableOptions);


        $list = [
            ['name' => 'Kilogram', 'unit' => 'kg', 'value' => 1],
            ['name' => 'Gram', 'unit' => 'g', 'value' => 1000.0000],
            ['name' => 'Pound', 'unit' => 'lb', 'value' => 2.2046],
            ['name' => 'Ounce', 'unit' => 'oz', 'value' => 35.2740],
        ];
        $id=1;
        foreach ($list as $key => $data) {
            $this->batchInsert('{{%shop__product_weight}}', ['value', 'title', 'unit'], [
                [$data['value'], $data['name'], $data['unit']]
            ]);
            $id++;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%shop__product_weight}}');
    }

}
