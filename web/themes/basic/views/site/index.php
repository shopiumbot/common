<?php
use panix\engine\Html;
$config = Yii::$app->settings->get('telegram');

?>
<div>
    <div class="jumbotron text-center">
        <h1>Welcome!</h1>
        <p class="lead">Telegram: <strong><?= Html::a('@'.$config->bot_name,'tg://@'.$config->bot_name); ?></strong></p>
    </div>
</div>
