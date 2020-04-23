<?php

namespace core\modules\images\migrations;

use panix\engine\db\Migration;
use core\modules\images\models\Image;

class m170622_111540_create_image_table extends Migration
{

    public function up()
    {
        $this->createTable(Image::tableName(), [
            'id' => $this->primaryKey(),
            'filePath' => $this->string(255)->notNull(),
            'path' => $this->string(255)->notNull(),
            'product_id' => $this->integer(),
            'is_main' => $this->boolean()->defaultValue(0),
            'name' => $this->string(80),
            'urlAlias' => $this->string(400)->notNull(),
            'ordern' => $this->integer()->unsigned(),
        ]);
        $this->createIndex('ordern', Image::tableName(), 'ordern');
        $this->createIndex('product_id', Image::tableName(), 'product_id');
        $this->createIndex('is_main', Image::tableName(), 'is_main');
    }

    public function down()
    {
        $this->dropTable(Image::tableName());
    }

}
