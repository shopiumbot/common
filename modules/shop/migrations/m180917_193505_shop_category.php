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

class m180917_193505_shop_category extends Migration {

    public function up()
    {
        $this->createTable(Category::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'tree' => $this->integer()->unsigned()->null(),
            'lft' => $this->integer()->unsigned()->notNull(),
            'rgt' => $this->integer()->unsigned()->notNull(),
            'depth' => $this->smallInteger(5)->unsigned()->notNull(),
            'name' => $this->string(255)->null(),
            'icon' => $this->char(1)->null(),
            'image' => $this->string(50)->null(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'switch' => $this->boolean()->defaultValue(1)->notNull(),
        ]);



        $this->createIndex('lft', Category::tableName(), 'lft');
        $this->createIndex('rgt', Category::tableName(), 'rgt');
        $this->createIndex('depth', Category::tableName(), 'depth');
        $this->createIndex('switch', Category::tableName(), 'switch');

    }

    public function down()
    {
        $this->dropTable(Category::tableName());
    }

}
