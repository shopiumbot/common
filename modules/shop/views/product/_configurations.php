<?php
use panix\engine\Html;
use yii\helpers\Json;

if (count($model->processVariants())) { ?>
    <div class="errors" id="productErrors"></div>

    <div class="configurations">
        <?php

        // Display product configurations
        if ($model->use_configurations) {
            // Get data
            $confData = $this->getConfigurableData();

            // Register configuration script

            $this->registerJs(strtr('var productPrices = {prices};', ['{prices}' => Json::encode($confData['prices'])]), \yii\web\View::POS_END);

            /* Yii::app()->clientScript->registerScript('productPrices', strtr('
                             var productPrices = {prices};
                         ', array(
                 '{prices}' => CJavaScript::encode($confData['prices'])
                     )), CClientScript::POS_END);*/
//echo CVarDumper::dump($confData,10,true);
            foreach ($confData['attributes'] as $attr) {
                // $attr->name .= $confData['prices'];
                if (isset($confData['data'][$attr->name])) {
                    echo '<div class="form-group row">';
                    echo Html::label($attr->title . ':', 'conf-' . $attr->name, ['class' => 'col-sm-3 col-form-label attr_name']);
                    echo ' <div class="col-sm-9">';
                    echo Html::dropDownList('configurations[' . $attr->name . ']', null, array_flip($confData['data'][$attr->name]), [
                        'id' => 'conf-' . $attr->name,
                        'class' => 'eavData custom-select w-auto'
                    ]);
                    echo '</div></div>';
                }
            }
        }
        ?>
    </div>

<?php } ?>