<?php

namespace core\modules\contacts\migrations;

/**
 * Generation migrate by PIXELION CMS
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 * Class m190330_104053_contacts
 */

use panix\engine\db\Migration;
use core\modules\contacts\models\Maps;

class m190330_104053_contacts extends Migration
{
    public $settingsForm = 'core\modules\contacts\models\SettingsForm';

    public function up()
    {
        $this->loadSettings();
    }

    public function down()
    {
    }

}
