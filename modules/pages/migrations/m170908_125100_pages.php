<?php

namespace core\modules\pages\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m170908_125100_pages
 */
use Yii;
use yii\db\Migration;
use core\modules\pages\models\Pages;

class m170908_125100_pages extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(Pages::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'name' => $this->string(255)->null(),
            'text' => $this->text()->null(),
            'views' => $this->integer()->defaultValue(0),
            'ordern' => $this->integer()->unsigned(),
            'switch' => $this->boolean()->defaultValue(1),
            'created_at' => $this->integer(11)->null(),
            'updated_at' => $this->integer(11)->null()
        ], $tableOptions);


        $this->createIndex('switch', Pages::tableName(), 'switch');
        $this->createIndex('ordern', Pages::tableName(), 'ordern');
        $this->createIndex('user_id', Pages::tableName(), 'user_id');

        $columns = ['name', 'text', 'user_id', 'ordern', 'created_at'];
        $this->batchInsert(Pages::tableName(), $columns, [
            ['О компании', 'test', 1, 1, time()],
            ['Тест', 'test', 1, 2, time()],
        ]);

    }

    public function down()
    {

        $this->dropTable(Pages::tableName());

    }

}
