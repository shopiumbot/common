<?php

namespace core\modules\shop;

use Yii;
use panix\engine\WebModule;
use yii\base\BootstrapInterface;
use panix\mod\admin\widgets\sidebar\BackendNav;

class Module extends WebModule implements BootstrapInterface
{

    public $icon = 'shopcart';

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $rules['catalog'] = 'shop/default/index';
        $rules['search/ajax'] = 'shop/search/ajax';
        $rules['shop/notify'] = 'shop/notify/index';
        $rules['shop/ajax/currency/<id:\d+>'] = 'shop/ajax/currency';
        $rules['manufacturer'] = 'shop/manufacturer/index';
        //$rules['manufacturer/<slug:[0-9a-zA-Z_\-]+>'] =  'shop/manufacturer/view';
        $rules['product/<slug:[0-9a-zA-Z\-]+>'] = 'shop/product/view';
        $rules['product/<id:\d+>/<action:[0-9a-zA-Z_\-]+>'] = 'shop/product/<action>';


        if (!($app instanceof \panix\engine\console\Application)) {
            $rules[] = [
                'class' => 'core\modules\shop\components\SearchUrlRule',
                //'pattern'=>'products/search',
                'route' => 'shop/search/index',
                'defaults' => ['q' => Yii::$app->request->get('q')]
            ];

            $rules[] = [
                'class' => 'core\modules\shop\components\ManufacturerUrlRule',
                'route' => 'shop/manufacturer/view',
                'index' => 'manufacturer',
                'pattern'=>'manufacturer/<slug:[0-9a-zA-Z_\-]+>'
            ];
            $rules[] = [
                'class' => 'core\modules\shop\components\CategoryUrlRule',
                'route' => 'shop/catalog/view',
                'index' => 'catalog',
                'pattern'=>'catalog/<slug:[0-9a-zA-Z_\-]+>',
                'alias' => 'full_path',
                //  'pattern' => ''
            ];

            /*$rules[] = [
                'class' => 'core\modules\shop\components\CategoryUrlRule',
            ];*/
        }

        $app->urlManager->addRules(
            $rules,
            false
        );
        $app->setComponents([
            'currency' => ['class' => 'core\modules\shop\components\CurrencyManager'],
        ]);

    }

    public function init()
    {
        if (Yii::$app->id == 'console') {
            $this->controllerNamespace = 'core\modules\shop\commands';
        }
        if (!(Yii::$app instanceof \yii\console\Application)) {
            parent::init();
        }
    }

    /**
     * @param bool|int $current_id
     * @return array|\core\modules\shop\models\Product[]
     */
    public function getViewProducts($current_id = false)
    {
        /** @var \core\modules\shop\models\Product $productModel */
        $productModel = Yii::$app->getModule('shop')->model('Product');
        $list = [];
        $session = Yii::$app->session->get('views');
        if (!empty($session)) {
            $ids = array_unique($session);
            if ($current_id) {
                $key = array_search($current_id, $ids);
                unset($ids[$key]);
            }
            $list = $productModel::find()->where(['id' => $ids])->all();
        }
        return $list;
    }


    public function getAdminMenu()
    {
        return [
            'shop' => [
                'label' => Yii::t('shop/default', 'MODULE_NAME'),
                'icon' => $this->icon,
                'items' => [
                    [
                        'label' => Yii::t('shop/admin', 'PRODUCTS'),
                        // 'url' => ['/admin/shop'], //bug with bootstrap 4.2.x
                        'icon' => $this->icon,
                        'items' => [
                            [
                                'label' => Yii::t('shop/admin', 'PRODUCT_LIST'),
                                "url" => ['/admin/shop/product'],
                                'icon' => 'list',
                            ],
                            [
                                'label' => Yii::t('shop/admin', 'CREATE_PRODUCT'),
                                "url" => ['/admin/shop/product/create'],
                                'icon' => 'add',
                            ]
                        ]
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'CATEGORIES'),
                        "url" => ['/admin/shop/category'],
                        'icon' => 'folder-open'
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'ATTRIBUTES'),
                        "url" => ['/admin/shop/attribute'],
                        'icon' => 'sliders'
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'TYPE_PRODUCTS'),
                        "url" => ['/admin/shop/type'],
                        'icon' => 't'
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'NOTIFIER'),
                        "url" => ['/admin/shop/notify'],
                        'icon' => 'envelope'
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'CURRENCY'),
                        "url" => ['/admin/shop/currency'],
                        'icon' => 'currencies'
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'MANUFACTURER'),
                        "url" => ['/admin/shop/manufacturer'],
                        'icon' => 'apple'
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'SUPPLIER'),
                        "url" => ['/admin/shop/supplier'],
                        'icon' => 'supplier'
                    ],
                    [
                        'label' => Yii::t('app/default', 'SETTINGS'),
                        "url" => ['/admin/shop/settings'],
                        'icon' => 'settings'
                    ],
                    'integration' => [
                        'label' => 'Интеграция',
                        'icon' => 'refresh',
                        'items' => []
                    ],
                ],
            ],
        ];
    }

    public function getAdminSidebar()
    {
        return (new BackendNav())->findMenu($this->id)['items'];
    }

    public function getDefaultModelClasses()
    {
        return [
            'Product' => '\core\modules\shop\models\Product',
            'Manufacturer' => '\core\modules\shop\models\Manufacturer',
            'Category' => '\core\modules\shop\models\Category',
        ];
    }

    public function getInfo()
    {
        return [
            'label' => Yii::t('shop/default', 'MODULE_NAME'),
            'author' => $this->getAuthor(),
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('shop/default', 'MODULE_DESC'),
            'url' => ['/admin/shop'],
        ];
    }

    public function getWidgetsList()
    {
        return [
            ''
        ];
    }

}
