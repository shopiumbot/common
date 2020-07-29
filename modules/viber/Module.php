<?php

namespace core\modules\viber;

use Yii;
use panix\engine\WebModule;
use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;

class Module extends WebModule implements BootstrapInterface
{

    public $icon = 'edit';

    public function bootstrap($app)
    {


        $groupUrlRule = new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => [
                '' => 'default/index',
                'set' => 'webhook/set',
                'hook' => 'webhook/hook',
                '<action:[0-9a-zA-Z_\-]+>' => 'default/<action>',
                '<controller:[0-9a-zA-Z_\-]+>' => '<controller>/index',
                '<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>' => '<controller>/<action>',
            ],
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);

    }


}
