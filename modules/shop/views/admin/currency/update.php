<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var $this \yii\web\View
 * @var $model \core\modules\shop\models\Currency
 */
?>
<div class="card">
    <div class="card-header">
        <h5 class="card-title"><?= $this->context->pageName ?></h5>
    </div>
    <?php
    if ($model->isNewRecord && !$model->currency) {


        echo Html::beginForm('', 'GET');


        ?>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-sm-4"><?= Html::activeLabel($model, 'currency', ['class' => 'control-label']); ?></div>
                <div class="col-sm-8">
                    <?php

                    $current_currencies = \core\modules\shop\models\Currency::find()->select('iso')->asArray()->createCommand()->queryColumn();

                    echo \panix\ext\bootstrapselect\BootstrapSelect::widget([
                        'model' => $model,
                        'attribute' => 'currency',
                        'items' => ArrayHelper::map(\core\components\models\Currencies::find()->cache(86400 * 30)->where(['NOT IN', 'iso', $current_currencies])->asArray()->all(), 'iso', function ($data) {
                            return html_entity_decode($data['iso'] . ' &mdash; ' . $data['name']);
                        }),
                        'jsOptions' => [
                            'liveSearch' => true,

                        ]
                    ])
                    ?>

                </div>
            </div>

        </div>
        <div class="card-footer text-center">
            <?= Html::submitButton(Yii::t('app/default', 'CREATE', 0), ['name' => false, 'class' => 'btn btn-success']); ?>
        </div>
        <?php
        echo Html::endForm();

    } else { ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal']
        ]);
        ?>

        <div class="card-body">
            <?php
            echo yii\bootstrap4\Tabs::widget([
                'items' => [
                    [
                        'label' => Yii::t('shop/admin', 'GENERAL'),
                        'content' => $this->render('_global', ['form' => $form, 'model' => $model]),
                        'active' => true,
                        'options' => ['id' => 'global'],
                    ],
                    [
                        'label' => Yii::t('shop/admin', 'PRICE_FORMAT'),
                        'content' => $this->render('_price', ['form' => $form, 'model' => $model]),
                        'options' => ['id' => 'price'],
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>

        <?php ActiveForm::end(); ?>

    <?php }
    ?>
</div>





