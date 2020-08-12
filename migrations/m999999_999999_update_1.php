<?php

use panix\engine\db\Migration;
use panix\engine\components\Settings;

class m999999_999999_update_1 extends Migration
{


    public function safeUp()
    {


        $settings[] = ['app', 'availability_hide', false];
        $settings[] = ['csv', 'pagenum', 250];
        $settings[] = ['csv', 'indent_row', 1];
        $settings[] = ['csv', 'indent_column', 1];
        $settings[] = ['csv', 'ignore_columns', NULL];

        $this->batchInsert(Settings::tableName(), ['category', 'param', 'value'], $settings);
    }


}
