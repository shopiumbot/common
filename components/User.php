<?php

namespace core\components;

use Yii;

class User extends \panix\mod\user\models\User
{

    public static function getDb()
    {
        return Yii::$app->serverDb;
    }
}