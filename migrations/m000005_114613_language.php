<?php

/**
 * Generation migrate by PIXELION CMS
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 * Class m000005_114613_language
 */
use panix\engine\db\Migration;
use shopium\mod\admin\models\Languages;

class m000005_114613_language extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';

        $this->createTable(Languages::tableName(), [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'code' => $this->string(5)->notNull(),
            'locale' => $this->string(5)->notNull(),
            'icon' => $this->string(5)->null(),
            'is_default' => $this->boolean()->defaultValue(0),
            'switch' => $this->boolean()->defaultValue(1),
            'ordern' => $this->integer(),
        ], $tableOptions);
        $this->createIndex('switch', Languages::tableName(), 'switch');
        $this->createIndex('ordern', Languages::tableName(), 'ordern');


        $this->batchInsert(Languages::tableName(), ['name', 'code', 'locale', 'icon', 'is_default', 'switch', 'ordern'], [
            ['Русский', 'ru', 'ru-RU', '🇷🇺', 1, 1, 1],
            ['English', 'en', 'en-US', '🇬🇧', 0, 1, 2],
            ['Український', 'uk', 'uk-UA', '🇺🇦', 0, 1, 3],
            ['Deutsch', 'de', 'de', '🇩🇪', 0, 0, 4],
            ['Беларуская', 'be', 'be', '🇧🇾', 0, 0, 5],
            ['Français', 'fr', 'fr', '🇫🇷', 0, 0, 6],
            ['Nederlands', 'nl', 'nl', '🇳🇱', 0, 0, 7],
            ['Català', 'ca', 'ca', '🏴', 0, 0, 8],
            ['Italiano', 'it', 'it', '🇮🇹', 0, 0, 9],
            ['한국어', 'ko', 'ko', '🇰🇷', 0, 0, 10],
            ['Polskie', 'pl', 'pl', '🇮🇩', 0, 0, 11],
            ['Português (Brazil)', 'pt-br', 'pt-BR', '🇵🇹', 0, 0, 12],
            ['Español', 'es', 'es', '🇪🇸', 0, 0, 13],
            ['Türkçe', 'tr', 'tr', '🇹🇷', 0, 0, 14],
            //no add bahasa melasy
        ]);

    }

    public function down()
    {
        $this->dropTable(Languages::tableName());
    }

}
