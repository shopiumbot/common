<?php

namespace core\modules\contacts\migrations;


use panix\engine\db\Migration;
use core\modules\contacts\models\SettingsForm;

class m000100_202401_settings extends Migration
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
