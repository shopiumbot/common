11<?php
use yii\helpers\Url;

Url::remember(); // сохраняем URL для последующего использования

?>


<?php

use app\modules\shop\widgets\categories\CategoriesWidget;
?>
<?= CategoriesWidget::widget([]) ?>
        