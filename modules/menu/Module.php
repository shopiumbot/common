<?php

namespace core\modules\menu;

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
                '<action:[0-9a-zA-Z_\-]+>' => 'default/<action>',
                '<controller:[0-9a-zA-Z_\-]+>' => '<controller>/index',
                '<controller:[0-9a-zA-Z_\-]+>/<action:[0-9a-zA-Z_\-]+>' => '<controller>/<action>',
            ],
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);

        /*$app->urlManager->addRules(
            [
                'page/<slug:[0-9a-zA-Z_\-]+>/page/<page:\d+>/per-page/<per-page:\d+>' => 'menu/default/view',
                'page/<slug:[0-9a-zA-Z_\-]+>/page/<page:\d+>' => 'menu/default/view',
                'page/<slug:[0-9a-zA-Z_\-]+>' => 'menu/default/view',

            ],
            true
        );*/
    }

    public function getAdminMenu()
    {
        return [
            'modules' => [
                'items' => [
                    [
                        'label' => Yii::t('menu/default', 'MODULE_NAME'),
                        'url' => ['/admin/menu'],
                        'icon' => $this->icon,
                        'visible' => Yii::$app->user->can('/menu/admin/default/index') || Yii::$app->user->can('/menu/admin/default/*')
                    ],
                ],
            ],
        ];
    }


    public function getInfo()
    {
        return [
            'label' => Yii::t('menu/default', 'MODULE_NAME'),
            'author' => 'dev@pixelion.com.ua',
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('menu/default', 'MODULE_DESC'),
            'url' => ['/admin/menu'],
        ];
    }

}
