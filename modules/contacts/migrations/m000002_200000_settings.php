<?php

namespace core\modules\contacts\migrations;


use panix\engine\db\Migration;
use core\modules\contacts\models\SettingsForm;

class m000002_200000_settings extends Migration
{
    public $settingsForm = SettingsForm::class;

    public function up()
    {
        $this->loadSettings();
    }

    public function down()
    {

    }

}
