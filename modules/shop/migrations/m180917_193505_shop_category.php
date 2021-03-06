<?php

namespace core\modules\shop\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m180917_193505_shop_category
 */
use Yii;
use panix\engine\db\Migration;
use core\modules\shop\models\Category;
use core\modules\shop\models\translate\CategoryTranslate;

class m180917_193505_shop_category extends Migration
{

    public function up()
    {

        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';

        $this->createTable(Category::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'tree' => $this->integer()->unsigned()->null(),
            'lft' => $this->integer()->unsigned()->notNull(),
            'rgt' => $this->integer()->unsigned()->notNull(),
            'depth' => $this->smallInteger(5)->unsigned()->notNull(),
           // 'name' => $this->string(255)->null(),
            'chunk' => $this->tinyInteger(1)->defaultValue(1),
            'icon' => $this->char(1)->null(),
            'path_hash' => $this->string(32)->null(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
        ], $tableOptions);

        $this->createIndex('path_hash', Category::tableName(), 'path_hash');
        $this->createIndex('lft', Category::tableName(), 'lft');
        $this->createIndex('rgt', Category::tableName(), 'rgt');
        $this->createIndex('depth', Category::tableName(), 'depth');
        $this->createIndex('switch', Category::tableName(), 'switch');

        $this->createTable(CategoryTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255)->null(),
        ], $tableOptions);

        $this->createIndex('object_id', CategoryTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', CategoryTranslate::tableName(), 'language_id');

        $this->addFk([CategoryTranslate::tableName(), 'object_id'], [Category::tableName(), 'id']);

    }

    public function down()
    {
        $this->dropTable(Category::tableName());
        $this->dropTable(CategoryTranslate::tableName());
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
