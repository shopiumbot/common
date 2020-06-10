<?php
use panix\engine\Html;
use core\modules\user\models\User;
use shopium\mod\telegram\components\Api;
use Longman\TelegramBot\Request;

$user = User::findOne(Yii::$app->params['client_id']);
$telegram = new Api($user->token);
$me = Request::getMe();
if($me->isOk()) {
    $result = $me->getResult();

    ?>

    <div>
        <div class="jumbotron text-center">
            <h1>Добро пожаловать!</h1>
            <p class="lead"><i class="icon-telegram-outline"></i>
                <strong><?= Html::a('@' . $result->username, 'tg://resolve?domain=' . $result->username, ['class' => 'text-danger']); ?></strong>
            </p>
            <p class="lead">Хотите такого же бота? вам <strong>&laquo;<a href="https://shopiumbot.com"
                                                                         class="text-danger">сюда</a>&raquo;</strong>
            </p>

        </div>
    </div>
    <?php

}else{
 throw new \yii\web\ForbiddenHttpException();
}