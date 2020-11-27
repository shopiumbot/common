<?php

namespace core\modules\menu\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m170518_135110_menu
 */

use Yii;
use panix\engine\db\Migration;
use core\modules\menu\models\Menu;
use core\modules\menu\models\MenuTranslate;


class m170518_135110_menu extends Migration
{

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';

        $this->createTable(Menu::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'menu_id' => $this->integer()->unsigned(),
           // 'page_id' => $this->integer()->unsigned(),
            'callback' => $this->string(255)->null(),
            'ordern' => $this->integer()->unsigned(),
            'switch' => $this->boolean()->defaultValue(1),
        ], $tableOptions);


        $this->createIndex('switch', Menu::tableName(), 'switch');
        $this->createIndex('ordern', Menu::tableName(), 'ordern');
        $this->createIndex('menu_id', Menu::tableName(), 'menu_id');
       // $this->createIndex('page_id', Menu::tableName(), 'page_id');

        $this->createTable(MenuTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255)->null(),
            'content' => $this->text()->null(),
        ], $tableOptions);

        $this->createIndex('object_id', MenuTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', MenuTranslate::tableName(), 'language_id');



        $columns = ['id', 'menu_id', 'callback', 'ordern', 'switch'];
        $columnsLang = ['object_id', 'language_id', 'name'];
     /*   $buttons = [
            [
                'menu_id' => 1,
                'callback' => 'start',
                'translates' => [
                    1 => Yii::$app->settings->get('app','button_text_start'),
                    2 => Yii::$app->settings->get('app','button_text_start'),
                    3 => Yii::$app->settings->get('app','button_text_start'),
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'catalog',
                'translates' => [
                    1 => Yii::$app->settings->get('app','button_text_catalog'),
                    2 => Yii::$app->settings->get('app','button_text_catalog'),
                    3 => Yii::$app->settings->get('app','button_text_catalog'),
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'history',
                'translates' => [
                    1 => Yii::$app->settings->get('app','button_text_history'),
                    2 => Yii::$app->settings->get('app','button_text_history'),
                    3 => Yii::$app->settings->get('app','button_text_history'),
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'search',
                'translates' => [
                    1 => Yii::$app->settings->get('app','button_text_search'),
                    2 => Yii::$app->settings->get('app','button_text_search'),
                    3 => Yii::$app->settings->get('app','button_text_search'),
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'cart',
                'translates' => [
                    1 => Yii::$app->settings->get('app','button_text_cart'),
                    2 => Yii::$app->settings->get('app','button_text_cart'),
                    3 => Yii::$app->settings->get('app','button_text_cart'),
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'settings',
                'translates' => [
                    1 => '⚙️ Настройки',
                    2 => '⚙️ Settings',
                    3 => '⚙️ Налаштування',
                ]
            ]
        ];*/


        $buttons = [
            [
                'menu_id' => 1,
                'callback' => 'start',
                'translates' => [
                    1 => '🏠 Начало',
                    2 => '🏠 Start',
                    3 => '🏠 Головна',
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'catalog',
                'translates' => [
                    1 => '📂 Каталог',
                    2 => '📂 Catalog',
                    3 => '📂 Каталог',
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'history',
                'translates' => [
                    1 => '📦 Мои заказы',
                    2 => '📦 My orders',
                    3 => '📦 Мої замовлення',
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'search',
                'translates' => [
                    1 => '🔎 Поиск',
                    2 => '🔎 Search',
                    3 => '🔎 Пошук',
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'cart',
                'translates' => [
                    1 => '🛍️ Корзина',
                    2 => '🛍️ Basket',
                    3 => '🛍️ Кошик',
                ]
            ],
            [
                'menu_id' => 1,
                'callback' => 'settings',
                'translates' => [
                    1 => '⚙️ Настройки',
                    2 => '⚙️ Settings',
                    3 => '⚙️ Налаштування',
                ]
            ]
        ];



        $dataLang = [];
        $data = [];
        foreach ($buttons as $key => $button) {
            $id = $key + 1;
            $data[] = [$id, $button['menu_id'], $button['callback'], $id, 1];
            foreach (\shopium\mod\admin\models\Languages::find()->all() as $language) {
                if (isset($button['translates'][$language->id]))
                    $dataLang[] = [$id, $language->id, $button['translates'][$language->id]];
            }

        }

        $this->batchInsert(Menu::tableName(), $columns, $data);
        $this->batchInsert(MenuTranslate::tableName(), $columnsLang, $dataLang);
    }

    public function down()
    {

        $this->dropTable(Menu::tableName());
        $this->dropTable(MenuTranslate::tableName());
    }

}
