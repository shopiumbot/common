<?php

namespace core\modules\shop\controllers\admin;

use panix\engine\CMS;
use Yii;
use core\components\controllers\AdminController;
use core\modules\shop\models\Category;
use yii\web\Response;

/**
 * AdminController implements the CRUD actions for User model.
 */
class CategoryController extends AdminController
{

    public $icon = 'folder-open';

    public function actions()
    {
        return [
            'rename-node' => [
                'class' => 'core\modules\shop\behaviors\nestedsets\actions\RenameNodeAction',
                'modelClass' => Category::class,
                'successMessage' => Category::t('NODE_RENAME_SUCCESS'),
                'errorMessage' => Category::t('NODE_RENAME_ERROR')
            ],
            'move-node' => [
                'class' => 'core\modules\shop\behaviors\nestedsets\actions\MoveNodeAction',
                'modelClass' => Category::class,
            ],
            'switch-node' => [
                'class' => 'panix\engine\behaviors\nestedsets\actions\SwitchNodeAction',
                'modelClass' => Category::class,
                'onMessage' => Category::t('NODE_SWITCH_ON'),
                'offMessage' => Category::t('NODE_SWITCH_OFF')
            ],
            'delete-node' => [
                'class' => 'panix\engine\behaviors\nestedsets\actions\DeleteNodeAction',
                'modelClass' => Category::class,
            ],
            'delete-file' => [
                'class' => \panix\engine\actions\DeleteFileAction::class,
                'modelClass' => Category::class,
                'saveMethod' => 'saveNode'
            ],
        ];
    }

    public function actionIndex()
    {
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Category $model
         */
        $model = Category::findModel(Yii::$app->request->get('id'));

        if ($model->getIsNewRecord()) {
            $this->pageName = Yii::t('shop/Category', 'CREATE_TITLE');
        } else {
            $this->pageName = Yii::t('shop/Category', 'UPDATE_TITLE', ['name' => $model->name]);
        }

        $this->pageName = Yii::t('shop/admin', 'CATEGORIES');
        $this->buttons = [
            [
                'label' => Yii::t('shop/admin', 'CREATE_CATEGORY'),
                'url' => ['/admin/shop/category'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label' => $this->module->info['label'],
            'url' => $this->module->info['url'],
        ];
        $this->breadcrumbs[] = $this->pageName;


        $post = Yii::$app->request->post();
        if (Yii::$app->request->get('parent_id')) {
            $model->parent_id = Category::findModel(Yii::$app->request->get('parent_id'));
        } else {
            $model->parent_id = Category::findModel(1);
        }
        if ($model->load($post) && $model->validate()) {

            if ($model->getIsNewRecord()) {


                $model->appendTo($model->parent_id);
                Yii::$app->session->setFlash('success', Yii::t('app/default', 'SUCCESS_UPDATE'));
                return $this->redirect(['/admin/shop/category/index']);
            } else {
                $model->saveNode();
                Yii::$app->session->setFlash('success', Yii::t('app/default', 'SUCCESS_UPDATE'));
                return $this->redirect(['/admin/shop/category/index', 'id' => $model->id]);
            }
        }


        return $this->render('index', [
            'model' => $model,
        ]);
    }


    public function actionCreateNode2()
    {
        /**
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Category $model
         * @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|Category $parent
         */
        $model = new Category;
        $parent = Category::findModel(Yii::$app->request->get('parent_id'));

        $model->name = $_GET['text'];
        if ($model->validate()) {
            $model->appendTo($parent);
            $message = Yii::t('shop/Category', 'CATEGORY_TREE_CREATE');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => $message,
        ];
    }


    /**
     * Redirect to category front.
     */
    public function actionRedirect()
    {
        $node = Category::findModel(Yii::$app->request->get('id'));
        return $this->redirect($node->getViewUrl());
    }

    public function actionCreateRoot()
    {

        Yii::$app->db->createCommand()->truncateTable(Category::tableName())->execute();

        $model = new Category;
        $model->name = 'Каталог продукции';
        $model->lft = 1;
        $model->rgt = 2;
        $model->depth = 1;
        $model->full_path = '';
        if ($model->validate()) {
            $model->saveNode();

            $model2 = new Category;
            $model2->name = 'Category 1';
            $model2->appendTo($model);


            $model2 = new Category;
            $model2->name = 'Category 2';
            $model2->appendTo($model);


            $model3 = new Category;
            $model3->name = 'Category 2-1';
            $model3->appendTo($model2);


            $model2 = new Category;
            $model2->name = 'Category 3';
            $model2->appendTo($model);

        } else {
            print_r($model->getErrors());
            die;
        }

        ///return $this->redirect('index');
    }
}
