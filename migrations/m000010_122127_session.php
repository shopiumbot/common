<?php

/**
 * Generation migrate by PIXELION CMS
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 * Class m000010_122127_session
 */
use panix\engine\db\Migration;

class m000010_122127_session extends Migration
{

    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%session}}', [
            'id' => \yii\db\Schema::TYPE_CHAR . '(40) NOT NULL',
            'user_id' => $this->integer()->null()->unsigned(),
            'expire' => $this->integer()->notNull(),
            'expire_start' => $this->integer()->null(),
            'data' => 'LONGBLOB',
            'ip' => $this->string(100),
            'user_type' => $this->string(50)->null(),
            'user_name' => $this->string(100)->null(),
            'user_agent' => $this->string(255)->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ],$this->tableOptions);
        $this->createIndex('user_id', '{{%session}}', 'user_id');
        $this->addPrimaryKey('id', '{{%session}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%session}}');
    }

}
