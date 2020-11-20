<?php

namespace core\modules\shop\migrations;
/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * 
 * Class m180917_193517_shop_currency
 */
use yii\db\Schema;
use panix\engine\db\Migration;
use core\modules\shop\models\Currency;

class m180917_193517_shop_currency extends Migration {

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(Currency::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->null(),
            'iso' => $this->string(10)->null()->defaultValue(null),
            'symbol' => $this->string(10)->notNull()->defaultValue(null),
            'rate' => $this->money(10,2)->notNull()->defaultValue(null),
            'rate_old' => $this->money(10,2),
            'penny' => $this->string(5)->null()->defaultValue(null),
            'separator_hundredth' => $this->string(5)->null()->defaultValue(null),
            'separator_thousandth' => $this->string(5)->null()->defaultValue(null),
            'is_main' => $this->boolean()->defaultValue(0),
            'is_default' => $this->boolean()->defaultValue(0),
            'switch' => $this->boolean()->notNull()->defaultValue(null),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'ordern' => $this->integer()->unsigned(),
        ],$tableOptions);

        $this->createIndex('is_main', Currency::tableName(), 'is_main');
        $this->createIndex('is_default', Currency::tableName(), 'is_default');
        $this->createIndex('ordern', Currency::tableName(), 'ordern');
        $this->createIndex('updated_at', Currency::tableName(), 'updated_at');

        $columns = ['name', 'iso', 'symbol', 'rate', 'penny', 'separator_hundredth', 'separator_thousandth', 'is_main', 'is_default', 'switch', 'created_at', 'ordern'];
        $this->batchInsert(Currency::tableName(), $columns, [
            ['Гривна', 'UAH', 'грн.', 1, 0, ' ', ' ', 1, 1, 1, time(), 1],
            ['Russian Ruble', 'RUB', 'p.', 1, 0, ' ', ' ', 0, 0, 0, time(), 2],
            ['United States Dollar', 'USD', '$', 1, 0, ' ', ' ', 0, 0, 0, time(), 3],
            ['Euro', 'EUR', '€', 1, 0, ' ', ' ', 0, 0, 0, time(), 4],
        ]);
    }

    public function down()
    {
        $this->dropTable(Currency::tableName());

    }

}
