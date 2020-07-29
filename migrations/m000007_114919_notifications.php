<?php

/**
 * Generation migrate by PIXELION CMS
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 * Class m000007_114919_notifications
 */
use panix\engine\db\Migration;
use shopium\mod\admin\models\Notification;

class m000007_114919_notifications extends Migration
{

    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        }
        $this->createTable(Notification::tableName(), [
            'id' => $this->primaryKey(),
            'type' => "ENUM('default', 'info', 'success', 'danger', 'warning')",
            'text' => $this->text(),
            'url' => $this->string(255),
            'sound' => $this->string(100)->defaultValue(NULL),
            'status' => $this->boolean()->defaultValue(0)->notNull(),
            'user_id_read' => $this->integer(),
            'created_at' => $this->integer(11)->null(),
        ],$this->tableOptions);
        $this->createIndex('user_id_read', Notification::tableName(), 'user_id_read');
    }

    public function down()
    {
        $this->dropTable(Notification::tableName());
    }

}
