<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\shop\widgets\categories\CategoriesWidget;
use app\modules\shop\widgets\filtersnew\FiltersWidget;

?>


<div class="catalog-container">
    <div class="catalog-sidebar">
        <?= CategoriesWidget::widget([]) ?>

        <div id="filters-container">
            <a class="d-md-none btn-filter-close close" href="javascript:void(0)"
               onclick="$('#filters-container').toggleClass('open'); return false;">
                <span>&times;</span>
            </a>

            <?php
            echo FiltersWidget::widget([
                'model' => $this->context->dataModel,
                'attributes' => $this->context->eavAttributes,

            ]);

            ?>
        </div>
    </div>
    <div class="catalog-content">
        <div class="heading-gradient">
            <h1><?= Html::encode(($this->h1) ? $this->h1 : $this->context->pageName); ?></h1>
        </div>
        <?php echo $this->render('@shop/views/catalog/_sorting', ['itemView' => $this->context->itemView]); ?>

        <div id="listview-ajax">
            <?php
            echo $this->render('@shop/views/catalog/listview', [
                'itemView' => $this->context->itemView,
                'provider' => $provider,
            ]);
            ?>
        </div>
    </div>
    <div class="clearfix"></div>
</div>