<?php

namespace app\modules\shop\api\v1;

use Yii;
use yii\helpers\VarDumper;

/**
 * Class Module
 * @package app\modules\shop\api\v1
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\shop\api\v1\controllers';

    public function init()
    {
        parent::init();
    }
}
