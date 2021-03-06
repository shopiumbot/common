<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193421_shop_attribute
 */

use Yii;
use panix\engine\db\Migration;
use core\modules\shop\models\Attribute;
use core\modules\shop\models\translate\AttributeTranslate;

class m180917_193421_shop_attribute extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        $this->createTable(Attribute::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'type' => $this->string(10)->notNull(),
            //'title' => $this->string(255)->notNull(),
            //'abbreviation' => $this->string(25)->null(),
            //'hint' => $this->text()->null(),
            'select_many' => $this->boolean()->defaultValue(0),
            'required' => $this->boolean()->defaultValue(0),
            'use_in_variants'=>$this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'sort' => $this->tinyInteger(1)->defaultValue(NULL),
            'display_on_pdf'=>$this->tinyInteger(1)->defaultValue(1),
            'display_on_list'=>$this->tinyInteger(1)->defaultValue(1),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
            'ordern' => $this->integer(11)->unsigned(),
        ],$tableOptions);
        $this->createIndex('name', Attribute::tableName(), 'name');
        $this->createIndex('ordern', Attribute::tableName(), 'ordern');
        $this->createIndex('switch', Attribute::tableName(), 'switch');


        $this->createTable(AttributeTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'title' => $this->string(255)->notNull(),
            'abbreviation' => $this->string(25)->null(),
            'hint' => $this->text()->null(),
        ], $tableOptions);

        $this->createIndex('object_id', AttributeTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', AttributeTranslate::tableName(), 'language_id');


        $this->addFk([AttributeTranslate::tableName(), 'object_id'], [Attribute::tableName(), 'id']);
    }

    public function down()
    {
        $this->dropTable(Attribute::tableName());
        $this->dropTable(AttributeTranslate::tableName());
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
