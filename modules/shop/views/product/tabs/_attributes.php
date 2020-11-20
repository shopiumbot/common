<?php

use panix\engine\Html;
use core\modules\shop\models\Attribute;

//$chosen = array(); // Array of ids to enable chosen
$attributes = (isset($model->type->shopAttributes)) ? $model->type->shopAttributes : [];


/*echo \panix\engine\barcode\BarcodeGenerator::widget([
    'elementId'=> 'showBarcode',
    'value'=> '4797111018719',
    'type'=>'ean8'
]);*/
?>

<div>
    <div class="form-group row">
        <div class="col-sm-3">
            <?= Html::activeLabel($model, 'weight', ['class' => 'col-form-label']); ?>
            <?= Html::activeTextInput($model, 'weight', ['class' => 'form-control']); ?>
        </div>
        <div class="col-sm-3">
            <?= Html::activeLabel($model, 'length', ['class' => 'col-form-label']); ?>
            <?= Html::activeTextInput($model, 'length', ['class' => 'form-control']); ?>
        </div>
        <div class="col-sm-3">
            <?= Html::activeLabel($model, 'width', ['class' => 'col-form-label']); ?>
            <?= Html::activeTextInput($model, 'width', ['class' => 'form-control']); ?></div>
        <div class="col-sm-3">
            <?= Html::activeLabel($model, 'height', ['class' => 'col-form-label']); ?>
            <?= Html::activeTextInput($model, 'height', ['class' => 'form-control']); ?>
        </div>
    </div>

    <h5 class="text-center mt-4 mb-4">Атрибуты</h5>
    <div class="row">
        <?php
        if (empty($attributes)) {
            echo \panix\engine\bootstrap\Alert::widget([
                'options' => ['class' => 'alert-info'],
                'body' => Yii::t('shop/admin', 'EMPTY_ATTRIBUTES_LIST')
            ]);

        } else {
            foreach ($attributes as $a) {
                echo '<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">';
                /** @var Attribute|\core\modules\shop\components\EavBehavior $a */
                // Repopulate data from POST if exists
                if (isset($_POST['Attribute'][$a->name])) {
                    $value = $_POST['Attribute'][$a->name];
                } else {

                    $value = $model->getEavAttribute($a->name);
                    // die('zz');
                }

                //$a->required ? $required = ' <span class="required">*</span>' : $required = null;

                if ($a->type == Attribute::TYPE_DROPDOWN) {
                    $addOptionLink = Html::a(Html::icon('add'), '#', [
                        'rel' => $a->id,
                        'data-name' => $a->getIdByName(), //$a->getIdByName()
                        //'data-name' => Html::getInputName($a, $a->name),
                        'onclick' => 'js: return addNewOption($(this));',
                        'class' => 'btn btn-success', // btn-sm mt-2 float-right
                        'title' => Yii::t('shop/admin', 'ADD_OPTION')
                    ]);

                    // . ' ' . Yii::t('shop/admin', 'ADD_OPTION')
                } else
                    $addOptionLink = null;

                $error = '';
                $inputClass = '';

                if ($a->required && array_key_exists($a->name, $model->getErrors())) {
                    $inputClass = 'is-invalid';
                    $error = Html::error($a, $a->name);
                }
                ?>
                <div class="form-group row <?= ($a->required ? 'required' : ''); ?>">
                    <?= Html::label($a->title, $a->name, ['class' => 'col-sm-4 col-form-label']); ?>
                    <div class="col-sm-8 rowInput eavInput">
                        <div class="input-group<?= ($a->type == Attribute::TYPE_CHECKBOX_LIST) ? '1' : ''; ?>">
                            <?= $a->renderField($value, $inputClass); ?>
                            <?php if ($a->abbreviation) { ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><?= $a->abbreviation; ?></span>
                                </div>
                            <?php } ?>

                            <?php if ($addOptionLink) { ?>
                                <div class="input-group-append">
                                    <?= $addOptionLink; ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?= $error; ?>
                    </div>
                </div>

                <?php
                //echo Html::beginTag('div', ['class' => 'form-group row ' . ($a->required ? 'required' : '')]);
                //echo Html::label($a->title, $a->name, ['class' => 'col-sm-4 col-form-label']);


//. $error . $addOptionLink
                //echo Html::endTag('div');
                echo '</div>';
            } // . Html::error($a, 'name', ['class' => 'text-danger'])


        }
        ?>
    </div>
</div>
