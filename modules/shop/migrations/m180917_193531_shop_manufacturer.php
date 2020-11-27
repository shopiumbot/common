<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193531_shop_manufacturer
 */

use core\modules\shop\models\translate\ManufacturerTranslate;
use Yii;
use panix\engine\CMS;
use panix\engine\db\Migration;
use core\modules\shop\models\Manufacturer;

class m180917_193531_shop_manufacturer extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(Manufacturer::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'cat_id' => $this->integer()->null(),
            //'name' => $this->string(255)->notNull(),
            'switch' => $this->boolean()->defaultValue(1),
            'ordern' => $this->integer()->unsigned(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);


        $this->createIndex('switch', Manufacturer::tableName(), 'switch');
        $this->createIndex('ordern', Manufacturer::tableName(), 'ordern');
        $this->createIndex('cat_id', Manufacturer::tableName(), 'cat_id');


        $this->createTable(ManufacturerTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255)->null()
        ], $tableOptions);
        $this->createIndex('object_id', ManufacturerTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', ManufacturerTranslate::tableName(), 'language_id');

        $this->addFk([ManufacturerTranslate::tableName(), 'object_id'], [Manufacturer::tableName(), 'id']);

        $brands = ['Apple', 'Asus', 'Samsung', 'LG', 'Philips', 'Lenovo', 'Sony', 'YAMAHA'];
        foreach ($brands as $key => $brand) {
            $s = new Manufacturer;
            $s->name = $brand;
            $s->save(false);
        }


        $this->loadColumns('grid-manufacturer', Manufacturer::class, ['name', 'products']);

    }

    public function down()
    {
        $this->dropTable(Manufacturer::tableName());
        $this->dropTable(ManufacturerTranslate::tableName());
    }


    public function addFk($table1 = array(), $table2 = array())
    {
        $gename = str_replace('{{%', '{{%fk_', $table1[0]);
        $gename = str_replace('}}', '_' . $table1[1] . '}}', $gename);
        $this->addForeignKey(
            $gename,
            $table1[0],
            $table1[1],
            $table2[0],
            $table2[1],
            'CASCADE',
            'CASCADE'
        );
    }
}
