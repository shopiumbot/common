<?php

use yii\helpers\Html;

$this->registerJs("
    $(function () {
        $('.te').click(function (e) {
            $.ajax({
                url:$(this).attr('href'),
                success:function (data) {
                    $('#listview-ajax').html(data);
                }
            });
            return false;
        })
    });
", \yii\web\View::POS_END);

?>
<script>

</script>
<div class="clearfix filters-container">
    <div class="row">


        <div class="col-sm-5 col-md-5 col-lg-5">

            <?php
            $sorter[Yii::$app->urlManager->removeUrlParam('/' . Yii::$app->requestedRoute, 'sort')] = Yii::t('shop/default', 'SORT');
            $sorter[Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['sort' => 'price'])] = Yii::t('shop/default', 'SORT_BY_PRICE_ASC');
            $sorter[Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['sort' => '-price'])] = Yii::t('shop/default', 'SORT_BY_PRICE_DESC');
            $sorter[Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['sort' => '-created_at'])] = Yii::t('shop/default', 'SORT_BY_DATE_DESC');
            $active = Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['sort' => Yii::$app->request->get('sort')]);

            echo Html::dropDownList('sort', $active, $sorter, ['onChange' => 'window.location = $(this).val()', 'class' => 'custom-select', 'style' => 'width:auto;']);
            ?>


        </div><!-- /.col -->
        <div class="col-sm-3 col-md-4 col-lg-4">


            <?php
            $limits = array(Yii::$app->urlManager->removeUrlParam('/' . Yii::$app->requestedRoute, 'per-page') => $this->context->allowedPageLimit[0]);
            array_shift($this->context->allowedPageLimit);
            foreach ($this->context->allowedPageLimit as $l) {
                $active = Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['per-page' => Yii::$app->request->get('per-page')]);
                $limits[Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['per-page' => $l])] = $l;
            }
            ?>
            <span class=""><?= Yii::t('shop/default', 'OUTPUT_ON'); ?> </span>
            <?php
            echo Html::dropDownList('per-page', $active, $limits, ['onChange' => 'window.location = $(this).val()', 'class' => 'custom-select', 'style' => 'width:auto;']);
            ?>
            <span class=""><?= Yii::t('shop/default', 'товаров'); ?></span>

        </div>


        <div class="col-sm-4 col-md-3 col-lg-3">

            <div class="btn-group btn-group-sm1">
                <a class="btn btn-outline-secondary <?php if ($itemView === '_view_grid') echo 'active'; ?>"
                   href="<?= Yii::$app->urlManager->removeUrlParam('/' . Yii::$app->requestedRoute, 'view') ?>"><i
                            class="icon-grid"></i></a>
                <a class="btn btn-outline-secondary <?php if ($itemView === '_view_list') echo 'active'; ?>"
                   href="<?= Yii::$app->urlManager->addUrlParam('/' . Yii::$app->requestedRoute, ['view' => 'list']) ?>"><i
                            class="icon-menu"></i></a>
            </div>
        </div>
    </div>
</div>
