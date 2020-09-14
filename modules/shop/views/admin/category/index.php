<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;
use core\modules\shop\models\Category;
use panix\engine\bootstrap\Alert;

/**
 * @var $form \panix\engine\bootstrap\ActiveForm
 * @var $model \core\modules\shop\models\Category
 */

?>

<div class="row">
    <div class="col-sm-12 col-md-7 col-lg-8">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>

            <div class="card-body">
                <?php
                if (Yii::$app->request->get('parent_id')) {
                    $parent = Category::findOne(Yii::$app->request->get('parent_id'));
                    echo Alert::widget([
                        'options' => [
                            'class' => 'alert-info',
                        ],
                        'body' => "Добавление в категорию: " . $parent->name,
                    ]);
                }
                ?>
                <?= $form->field($model, 'chunk')->dropDownList([1 => 1, 2 => 2, 3 => 3]) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'icon')->textInput(['maxlength' => 5])->hint('Пример: 📂') ?>

            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app/default', 'CREATE') : Yii::t('app/default', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-sm-12 col-md-5 col-lg-4">
        <?= $this->render('_category', ['model' => $model]); ?>
    </div>
</div>
