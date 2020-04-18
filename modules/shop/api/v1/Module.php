<?php

namespace core\modules\shop\api\v1;

use Yii;
use yii\helpers\VarDumper;

/**
 * Class Module
 * @package core\modules\shop\api\v1
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'core\modules\shop\api\v1\controllers';

    public function init()
    {
        parent::init();
    }
}
