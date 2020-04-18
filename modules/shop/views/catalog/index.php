<?php
use yii\helpers\Url;
use core\modules\shop\widgets\categories\CategoriesWidget;
Url::remember(); // сохраняем URL для последующего использования

?>

<?= CategoriesWidget::widget([]) ?>
