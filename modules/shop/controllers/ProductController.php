<?php

namespace core\modules\shop\controllers;

use core\modules\shop\components\EavBehavior;
use core\modules\shop\models\Category;
use panix\engine\CMS;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use core\modules\shop\models\Product;
use core\modules\shop\models\search\ProductSearch;
use core\components\controllers\AdminController;
use core\modules\shop\models\ProductType;
use core\modules\shop\models\Attribute;
use core\modules\shop\models\AttributeOption;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\Response;

class ProductController extends AdminController
{

    public $tab_errors = [];
    public $count;
    public $created = true;


    public function actions()
    {
        return [
            'sortable' => [
                'class' => 'panix\engine\grid\sortable\Action',
                'modelClass' => Product::class,
                'successMessage' => Yii::t('shop/admin', 'SORT_PRODUCT_SUCCESS_MESSAGE')
            ],
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Product::class,
            ],
            'switch' => [
                'class' => 'panix\engine\actions\SwitchAction',
                'modelClass' => Product::class,
            ],
        ];
    }

    public function actionRenderVariantTable()
    {
        $attribute = Attribute::findOne($_GET['attr_id']);

        if (!$attribute)
            $this->error404(Yii::t('shop/admin', 'ERR_LOAD_ATTR'));

        return $this->renderPartial('tabs/variants/_table', array(
            'attribute' => $attribute
        ));
    }

    public function beforeAction($action)
    {

        if (in_array($action->id, ['render-products-price-window', 'set-products', 'render-duplicate-products-window'])) {
            $this->enableCsrfValidation = false;
        }

        /*
                    $this->count = Product::find()->count();
                    if ($this->count >= Yii::$app->params['plan'][Yii::$app->user->planId]['product_limit']) {
                        $this->created = false;
                    }


                    if (in_array($action->id, ['create'])) {
                        if (!$this->created) {
                            throw new HttpException(403, Yii::t('shop/default', 'PRODUCT_LIMIT', $this->count));
                        }
                    }
        */
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {


        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->pageName = Yii::t('shop/admin', 'PRODUCTS');
        if ($this->created) {
            $this->buttons = [
                [
                    'icon' => 'add',
                    'label' => Yii::t('shop/admin', 'CREATE_PRODUCT'),
                    'url' => ['create'],
                    'options' => ['class' => 'btn btn-success']
                ]
            ];
        }


        if (isset(Yii::$app->request->getQueryParams()['ProductSearch'])) {
            if (isset(Yii::$app->request->getQueryParams()['ProductSearch']['search_string'])) {
                $this->view->params['breadcrumbs'][] = [
                    'label' => Yii::t('shop/admin', 'PRODUCTS'),
                    'url' => ['/admin/shop/product'],
                ];
                $this->pageName = Yii::t('shop/default', 'SEARCH_RESULT', [
                    'query' => Yii::$app->request->getQueryParams()['ProductSearch']['search_string'],
                    'count' => $dataProvider->count
                ]);
            }
        }

        $this->view->params['breadcrumbs'][] = $this->pageName;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {
        /** @var Product|\core\modules\images\behaviors\ImageBehavior $model */
        $model = Product::findModel($id);
        $isNew = $model->isNewRecord;
        $this->pageName = Yii::t('shop/default', 'MODULE_NAME');

        $post = Yii::$app->request->post();


        if (Yii::$app->request->get('Product'))
            $model->attributes = Yii::$app->request->get('Product');


        // On create new product first display "Choose type" form first.
        if ($isNew && isset($_GET['Product']['type_id'])) {
            // $type_id = $model->type_id;

            if (ProductType::find()->where(['id' => $model->type_id])->count() === 0)
                $this->error404(Yii::t('shop/admin', 'ERR_PRODUCT_TYPE'));
        }


        //if ($model->mainCategory)
        //    $model->main_category_id = $model->mainCategory->id;


        // Or set selected category from type pre-set.
        if ($model->type && !Yii::$app->request->isPost && $model->isNewRecord) {
            $model->main_category_id = $model->type->main_category;
        }

        //$model->setScenario("admin");


        $title = ($isNew) ? Yii::t('shop/admin', 'CREATE_PRODUCT') :
            Yii::t('shop/admin', 'UPDATE_PRODUCT');

        if ($model->type)
            $title .= ' "' . Html::encode($model->type->name) . '"';

        $this->pageName = $title;


        //print_r(Yii::$app->request->post('categories'));
        //print_r($_POST['categories']);die;

        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('shop/admin', 'PRODUCTS'),
            'url' => ['index']
        ];
        $this->view->params['breadcrumbs'][] = $title;


        if ($model->load($post) && $model->validate() && $this->validateAttributes($model) && $this->validatePrices($model)) {


            if ($model->save()) {
                $model->file = \yii\web\UploadedFile::getInstances($model, 'file');
                $data = [];
                if ($model->file) {

                    foreach ($model->file as $file) {
                        $image = $model->attachImage($file);
                        $data[] = [
                            'filePath' => $image->filePath,
                            'is_main' => $image->is_main
                        ];
                    }

                    // $model->images_data = json_encode($data);
                } else {
                    foreach ($model->images as $image) {
                        $data[] = [
                            'filePath' => $image->filePath,
                            'is_main' => $image->is_main
                        ];
                    }
                    //  $model->images_data = json_encode($data);
                }
                $mainCategoryId = 1;
                if (isset(Yii::$app->request->post('Product')['main_category_id']))
                    $mainCategoryId = Yii::$app->request->post('Product')['main_category_id'];

                if (true) { //Yii::$app->settings->get('shop', 'auto_add_subcategories')
                    // Авто добавление в предков категории
                    // Нужно выбирать в админки самую последнию категории по уровню.
                    $category = Category::findOne($mainCategoryId);
                    $categories = [];
                    if ($category) {
                        $tes = $category->ancestors()->excludeRoot()->all();
                        foreach ($tes as $cat) {
                            $categories[] = $cat->id;
                        }

                    }
                    $categories = ArrayHelper::merge($categories, Yii::$app->request->post('categories', []));
                } else {

                    $categories = Yii::$app->request->post('categories', []);
                }

                $model->setCategories($categories, $mainCategoryId);


                if (isset(Yii::$app->request->post('Product')['prices']) && !empty(Yii::$app->request->post('Product')['prices'])) {
                    $model->processPrices(Yii::$app->request->post('Product')['prices']);
                }
                $this->processAttributes($model);

            }


            return $this->redirectPage($isNew, $post);
        } else {

            // print_r($model->getErrors());
            foreach ($model->getErrors() as $key => $error) {
                Yii::$app->session->setFlash('error', $error[0]);
            }

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function validatePrices(Product $model)
    {
        $pricesPost = Yii::$app->request->post('ProductPrices', array());

        $errors = false;
        $orderFrom = [];

        foreach ($pricesPost as $index => $price) {
            $orderFrom[] = $price['from'];
            if ($price['value'] >= $model->price) {
                $errors = true;
                $model->addError('price', $model::t('ERROR_PRICE_MAX_BASIC'));
            }
        }

        if (count($orderFrom) !== count(array_unique($orderFrom))) {
            $errors = true;
            $model->addError('price', $model::t('ERROR_PRICE_DUPLICATE_ORDER_FROM'));
        }

        return !$errors;
    }

    public function actionAddOptionToAttribute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attribute = Attribute::findOne($_GET['attr_id']);

        if (!$attribute)
            $this->error404(Yii::t('shop/admin', 'ERR_LOAD_ATTR'));

        $attributeOption = new AttributeOption;
        $attributeOption->attribute_id = $attribute->id;
        $attributeOption->value = $_GET['value'];
        $attributeOption->save(false);

        return [
            'message' => 'Опция успешно добавлена',
            'id' => $attributeOption->id
        ];
    }


    /**
     * Validate required shop attributes
     * @param Product $model
     * @return bool
     */
    public function validateAttributes(Product $model)
    {
        $attributes = $model->type->shopAttributes;

        if (empty($attributes)) {
            return true;
        }

        $errors = false;
        foreach ($attributes as $attr) {
            if ($attr->required && empty($_POST['Attribute'][$attr->type][$attr->name])) {
                $this->tab_errors['attributes'] = true;
                $errors = true;
                $model->addError($attr->name, Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $attr->title]));
                //$attr->addError($attr->name, Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $attr->title]));
            }
        }

        return !$errors;
    }

    protected function processAttributes(Product $model)
    {
        $attributes = Yii::$app->request->post('Attribute', []);
        if (empty($attributes))
            return false;


        /**
         * @var EavBehavior|Product $deleteModel
         * @var EavBehavior|Product $model
         */
        $deleteModel = Product::findOne($model->id);
        $deleteModel->deleteEavAttributes([], true);
        // Delete empty values
        /*foreach ($attributes as $key => $val) {
            if (is_string($val) && $val === '') {
                unset($attributes[$key]);
            }
        }*/

        $reAttributes = [];
        foreach ($attributes as $key => $val) {

            if (in_array($key, [Attribute::TYPE_TEXT, Attribute::TYPE_TEXTAREA, Attribute::TYPE_YESNO])) {
                foreach ($val as $k => $v) {
                    $reAttributes[$k] = '"' . $v . '"';
                    if (is_string($v) && $v === '') {
                        unset($reAttributes[$k]);
                    }
                }
            } else {
                foreach ($val as $k => $v) {
                    $reAttributes[$k] = $v;
                    if (is_string($v) && $v === '') {
                        unset($reAttributes[$k]);
                    }
                }
            }
        }


        return $model->setEavAttributes($reAttributes, true);
    }

    /**
     * Render popup windows
     *
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionRenderCategoryAssignWindow()
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('window/category_assign_window');
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }


    public function actionDuplicateProducts_TEST()
    {
        $result['success'] = false;
        if (Yii::$app->request->isAjax) {

            if (Yii::$app->request->isPost) {
                $this->enableCsrfValidation = false;
                Yii::$app->response->format = Response::FORMAT_JSON;
                //TODO: return ids to find products
                $product_ids = Yii::$app->request->post('products', []);
                parse_str(Yii::$app->request->post('duplicate'), $duplicates);

                if (!isset($duplicates['copy']))
                    $duplicates['copy'] = [];

                $duplicator = new \core\modules\shop\components\ProductsDuplicator;
                $ids = $duplicator->createCopy($product_ids, $duplicates['copy']);
                //return $this->redirect('/admin/shop/product/?Product[id]=' . implode(',', $ids));
                $result['success'] = true;
                $result['message'] = 'Копия упешно создана';
                return $result;
            } else {
                return $this->renderAjax('window/duplicate_products_window');
            }


        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }

    /**
     * Render popup windows
     *
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionRenderDuplicateProductsWindow()
    {
        if (Yii::$app->request->isAjax) {
            return $this->render('window/duplicate_products_window');
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }

    /**
     * Duplicate products
     */
    public function actionDuplicateProducts()
    {
        $result['success'] = false;

        if (Yii::$app->request->isAjax) {
            //TODO: return ids to find products
            $product_ids = Yii::$app->request->post('products', []);
            parse_str(Yii::$app->request->post('duplicate'), $duplicates);

            if (!isset($duplicates['copy']))
                $duplicates['copy'] = [];

            $duplicator = new \core\modules\shop\components\ProductsDuplicator;
            $ids = $duplicator->createCopy($product_ids, $duplicates['copy']);
            if ($ids) {
                $result['success'] = true;
                $result['message'] = 'Копия упешно создана';
            } else {
                $result['message'] = 'Ошибка копирование.';
            }
            //return $this->redirect('/admin/shop/product/?Product[id]=' . implode(',', $ids));

            return $this->asJson($result);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * Render popup windows
     *
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionRenderProductsPriceWindow()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Product;
            return $this->render('window/products_price_window', ['model' => $model]);
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }

    /**
     * Set price products
     *
     * @throws ForbiddenHttpException
     */
    public function actionSetProducts()
    {
        $result['success'] = false;
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $product_ids = $request->post('products', []);
            parse_str($request->post('data'), $price);
            $products = Product::findAll($product_ids);
            foreach ($products as $p) {
                if (isset($p)) {
                    if (!$p->currency_id || !$p->use_configurations) { //запрещаем редактирование товаров с привязанной ценой и/или концигурациями
                        $p->price = $price['Product']['price'];
                        $p->save(false);
                        $result['success'] = true;
                        $result['message'] = 'Цена успешно изменена';
                    }
                }
            }
            return $this->asJson($result);
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }


    /**
     * Assign categories to products
     *
     * @return Response|boolean
     * @throws ForbiddenHttpException
     */
    public function actionAssignCategories()
    {
        //$this->enableCsrfValidation=false;
        if (Yii::$app->request->isAjax) {
            $categories = Yii::$app->request->post('category_ids');
            $products = Yii::$app->request->post('product_ids');

            if (empty($categories) || empty($products))
                return false;

            $products = Product::find()->where(['id' => $products])->all();

            foreach ($products as $p) {
                /** @var Product $p */
                $p->setCategories($categories, Yii::$app->request->post('main_category'));
            }
            return $this->asJson(['message' => 'Выбранным товарам категории изменены']);
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionUpdateIsActive()
    {
        if (Yii::$app->request->isAjax) {
            $ids = Yii::$app->request->post('ids');
            $switch = (int)Yii::$app->request->post('switch');
            $models = Product::find()->where(['id' => $ids])->all();
            foreach ($models as $product) {
                /** @var Product $product */
                if (in_array($switch, [0, 1])) {
                    $product->switch = $switch;
                    $product->save();
                }
            }
            return $this->asJson(['message' => Yii::t('app/default', 'SUCCESS_UPDATE')]);
        } else {
            throw new ForbiddenHttpException();
        }
    }


}
