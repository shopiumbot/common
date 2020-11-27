<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193433_shop_attribute_option
 */

use panix\engine\db\Migration;
use core\modules\shop\models\AttributeOption;
use core\modules\shop\models\translate\AttributeOptionTranslate;

class m180917_193433_shop_attribute_option extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(AttributeOption::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'attribute_id' => $this->integer()->null(),
            'data' => $this->text()->null(),
            //'value' => $this->string(255)->notNull(),
            'ordern' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('attribute_id', AttributeOption::tableName(), 'attribute_id');
        $this->createIndex('ordern', AttributeOption::tableName(), 'ordern', 0);


        $this->createTable(AttributeOptionTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'value' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createIndex('object_id', AttributeOptionTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', AttributeOptionTranslate::tableName(), 'language_id');

        $this->addFk([AttributeOptionTranslate::tableName(), 'object_id'], [AttributeOption::tableName(), 'id']);
    }

    public function down()
    {
        $this->dropTable(AttributeOption::tableName());
        $this->dropTable(AttributeOptionTranslate::tableName());
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
