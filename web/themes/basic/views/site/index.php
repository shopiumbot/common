<?php
use panix\engine\Html;

$config = Yii::$app->settings->get('telegram');

?>

<div>
    <div class="jumbotron text-center">
        <h1>Добро пожаловать!</h1>
        <?php if (!isset($config->bot_name) && !empty($config->bot_name)) { ?>
            <p class="lead"><i class="icon-telegram-outline"></i>
                <strong><?= Html::a('@' . $config->bot_name, 'tg://@' . $config->bot_name, ['class' => 'text-danger']); ?></strong>
            </p>
        <?php } ?>

        <p class="lead">Хотите такого же бота? вам <strong>&laquo;<a href="https://shopiumbot.com" class="text-danger">сюда</a>&raquo;</strong>
        </p>

    </div>
</div>
