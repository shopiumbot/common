<?php

namespace core\modules\shop\controllers;

use Yii;
use core\components\controllers\AdminController;
use core\modules\shop\models\Attribute;
use core\modules\shop\models\search\AttributeSearch;
use core\modules\shop\models\AttributeOption;
use yii\web\Response;

class AttributeController extends AdminController
{

    public $icon = 'sliders';

    public function actions()
    {
        return [
            'sortableOptions' => [
                'class' => 'panix\engine\grid\sortable\Action',
                'modelClass' => AttributeOption::class,
            ],
            'sortable' => [
                'class' => 'panix\engine\grid\sortable\Action',
                'modelClass' => Attribute::class,
            ],
            'switch' => [
                'class' => 'panix\engine\actions\SwitchAction',
                'modelClass' => Attribute::class,
            ],
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Attribute::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new AttributeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('shop/admin', 'CREATE_ATTRIBUTE'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        $this->pageName = Yii::t('shop/admin', 'ATTRIBUTES');
        $this->view->params['breadcrumbs'][] = $this->pageName;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Update attribute
     * @param bool $id
     * @return string
     */
    public function actionUpdate($id = false)
    {
        $model = Attribute::findModel($id, Yii::t('shop/admin', 'NO_FOUND_ATTR'));

        $this->pageName = ($model->isNewRecord) ? $model::t('CREATE_ATTRIBUTES') : $model::t('UPDATE_ATTRIBUTES');
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('shop/admin', 'ATTRIBUTES'),
            'url' => ['index']
        ];
        $this->view->params['breadcrumbs'][] = $this->pageName;


        $post = Yii::$app->request->post();

        $isNew = $model->isNewRecord;
        if (isset(Yii::$app->request->get('Attribute')['type']))
            $model->type = Yii::$app->request->get('Attribute')['type'];
        if ($model->load($post) && $model->validate() && $model->validateOptions()) { // && $model->validateOptions()
            $model->save();
            $this->saveOptions($model);

           // if ($isNew) {
           //     $this->redirect(['update', 'id' => $model->id]);
            //} else {
                return $this->redirectPage($isNew, $post);
            //}
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Save attribute options
     * @param Attribute $model
     */
    protected function saveOptions($model)
    {
        $dontDelete = [];
        $post = Yii::$app->request->post('options');
        //CMS::dump($post);die;
        if ($post) {
            foreach ($post as $id => $data) {


                if (isset($data[0]) && $data[0] != '' && !empty($data[0])) {
                    $index = 0;
                    $attributeOption = AttributeOption::find()
                        ->where(['id' => $id, 'attribute_id' => $model->id])
                        ->one();

                    if (!$attributeOption) {
                        $attributeOption = new AttributeOption;
                        $attributeOption->attribute_id = $model->id;


                    }
                    if (isset($data['data']) && is_array($data['data'])) {
                        foreach ($data['data'] as $k => $d) {
                            if (!empty($d['color'])) {
                                unset($data['data']['color'][$k]);
                            }
                        }
                        $attributeOption->data = serialize($data['data']);
                    } else {
                        $attributeOption->data = NULL;
                    }
                    $attributeOption->value = $data[$index];
                    $attributeOption->save(false);


                  //  foreach (Yii::$app->languageManager->languages as $lang) {
                        /*$attributeLangOption = AttributeOption::find()
                            ->translate($lang->id)
                            ->where([AttributeOption::tableName() . '.id' => $attributeOption->id])
                            ->one();*/


                       /* $attributeLangOption = AttributeOptionTranslate::find()
                            ->where(['object_id' => $attributeOption->id, 'language_id' => $lang->id])
                            ->one();

                        if (!$attributeLangOption) {
                            $attributeLangOption = new AttributeOptionTranslate;
                            $attributeLangOption->object_id = $attributeOption->id;
                            $attributeLangOption->language_id = $lang->id;

                        }


                        $attributeLangOption->value = $data[$index];
                        $attributeLangOption->save(false);*/

                        ++$index;
                   // }
                    array_push($dontDelete, $attributeOption->id);
                }
            }
        }

        if (count($dontDelete)) {
            $optionsToDelete = AttributeOption::find()->where([
                'AND', 'attribute_id=' . $model->id,
                ['NOT IN', 'id', $dontDelete]
            ])->all();
        } else {
            // Clear all attribute options
            $optionsToDelete = AttributeOption::find()->where(['attribute_id' => $model->id])->all();
        }


        if (!empty($optionsToDelete)) {
            foreach ($optionsToDelete as $o) {
                $o->delete();
            }
        }
    }

    protected function saveOptions2($model)
    {
        $dontDelete = [];
        if (!empty($_POST['options'])) {

            foreach ($_POST['options'] as $id => $val) {
                if (isset($val[0]) && $val[0] != '' && !empty($val[0])) {
                    $index = 0;
                    $attributeOption = AttributeOption::find()
                        ->where(['id' => $id, 'attribute_id' => $model->id])
                        ->one();

                    if (!$attributeOption) {
                        $attributeOption = new AttributeOption;
                        $attributeOption->attribute_id = $model->id;
                    }
                    $attributeOption->save(false);


                    foreach (Yii::$app->languageManager->languages as $lang) {
                        /*$attributeLangOption = AttributeOption::find()
                            ->translate($lang->id)
                            ->where([AttributeOption::tableName() . '.id' => $attributeOption->id])
                            ->one();*/


                        $attributeLangOption = AttributeOptionTranslate::find()
                            ->where(['object_id' => $attributeOption->id, 'language_id' => $lang->id])
                            ->one();

                        if (!$attributeLangOption) {
                            $attributeLangOption = new AttributeOptionTranslate;
                            $attributeLangOption->object_id = $attributeOption->id;
                            $attributeLangOption->language_id = $lang->id;

                        }


                        $attributeLangOption->value = $val[$index];
                        $attributeLangOption->save(false);

                        ++$index;
                    }
                    array_push($dontDelete, $attributeOption->id);
                }
            }
        }

        if (count($dontDelete)) {
            $optionsToDelete = AttributeOption::find()->where([
                'AND', 'attribute_id=' . $model->id,
                ['NOT IN', 'id', $dontDelete]
            ])->all();
        } else {
            // Clear all attribute options
            $optionsToDelete = AttributeOption::find()->where(['attribute_id' => $model->id])->all();
        }


        if (!empty($optionsToDelete)) {
            foreach ($optionsToDelete as $o) {
                $o->delete();
            }
        }
    }

    /**
     * Delete attribute
     *
     * @param array $id
     * @return \yii\web\Response
     */
    public function actionDelete($id = [])
    {
        if (Yii::$app->request->isPost) {
            $model = Attribute::find()->where(['id' => $id])->all();

            if (!empty($model)) {
                foreach ($model as $m) {
                    // $count = Product::find()->withEavAttributes(array($m->name))->count();
                    //if ($count)
                    //    throw new \yii\web\HttpException(503, Yii::t('shop/admin', 'ERR_DEL_ATTR'));
                    $m->delete();
                }
            }

            if (!Yii::$app->request->isAjax)
                return $this->redirect('index');
        }
    }


}
