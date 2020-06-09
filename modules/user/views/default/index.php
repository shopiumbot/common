<?php

use panix\engine\Html;
use panix\engine\CMS;
use panix\engine\bootstrap\ActiveForm;
use panix\engine\helpers\TimeZoneHelper;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use shopium\mod\telegram\models\Chat;
/**
 * @var yii\web\View $this
 * @var core\modules\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$telegram = new \shopium\mod\telegram\components\Api($model->token);
$me = Request::getMe();
$webhook_info = Request::getWebhookInfo();


//$2y$13$d4cUPV11ig3GB03YsQGh8eX7V6jItdM58fLXpMEXavlkdfd3oiOBG

$chats = Chat::find()->asArray()->all();
if ($chats) {
    foreach ($chats as $chat) {
        /*$send = Request::sendMessage([
            'chat_id'=>$chat['id'],
            'text'=>'test'
        ]);*/
    }
    /*$venue = Request::sendVenue([
        'chat_id' => $chat['id'],
        'latitude' => 46.3974947,
        'longitude' => 30.7125803,
        'title' => 'Pixelion',
        'address' => 'Pixelion address',
    ]);*/

    $keyboards[] = [
        new InlineKeyboardButton([
            'text' => 'Pay 1.00UAH',
            'callback_data' => "cartDelete"
        ]),
        new InlineKeyboardButton([
            'text' => '—',
            'callback_data' => "spinner/down/cart"
        ]),

    ];
    /*$invoice = Request::sendInvoice([
        'chat_id' => $chat['id'],
        'title' => 'title',
        'description' => 'description',
        'payload' => 'order-id',
        'provider_token' => '632593626:TEST:i56982357197',
        'start_parameter' => 'start_parameter',
        'currency' => 'UAH',
        'prices' => [
            new \Longman\TelegramBot\Entities\Payments\LabeledPrice([
                'label' => 'test',
                'amount' => 100
            ]),
        ],

        'disable_notification' => false,
        'reply_markup' => new \Longman\TelegramBot\Entities\InlineKeyboard([
            'inline_keyboard' => $keyboards
        ])

    ]);*/

    //\panix\engine\CMS::dump($invoice);

}
$time = strtotime("+1 month",strtotime('08-06-2020'));
echo $time.'<br>';
echo date('Y-m-d H:i:s',$time);
?>
<?php if (Yii::$app->session->hasFlash('success-webhook')) { ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success-webhook'); ?>
    </div>
<?php } ?>

<?php if ($flash = Yii::$app->session->getFlash("success")) { ?>
    <div class="alert alert-success">
        <?= $flash ?>
    </div>
<?php } ?>


<?php if (!$model->status && !$model->isNewRecord) { ?>
    <div class="alert alert-warning">
        Аккаунет не
        актевирован. <?= Html::a('отправить владельцу письмо с инструкций?', ['send-active', 'id' => $user->id]); ?>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-7 col-lg-6 col-xl-7">
        <?php

        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        ?>
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <div class="card-body">

                <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'token')->textInput(['maxlength' => 255]) ?>

                <?= $form->field($model, 'phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>
                <?= $form->field($model, 'gender')->dropDownList([0 => $model::t('FEMALE'), 1 => $model::t('MALE')], ['prompt' => 'Не указано']); ?>
                <?= $form->field($model, 'timezone')->dropDownList(TimeZoneHelper::getTimeZoneData(), ['prompt' => 'Не указано']); ?>
                <?= $form->field($model, 'bot_admins')
                    ->widget(\panix\ext\taginput\TagInput::class, ['placeholder' => 'ID'])
                    ->hint('Введите ID и нажмите Enter');
                ?>

                <?php if ($model->isNewRecord) { ?>
                    <?= $form->field($model, 'new_password')->passwordInput(); ?>
                    <?= $form->field($model, 'password_confirm')->passwordInput(); ?>
                <?php } ?>


            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton(Yii::t('app/default', $model->isNewRecord ? 'CREATE' : 'SAVE'), ['class' => 'btn btn-success']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-5 col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-header">
                <?php
                if ($me->isOk()) { ?>
                    Подключен бот: <?= Html::a($me->getResult()->first_name, 'tg://resolve?domain=' . $me->getResult()->username); ?>
                <?php } else { ?>
                    Бот не подключен!
                <?php } ?>
                <div class="float-right">
                    <?php

                    if ($webhook_info->isOk()) {
                        $result = $webhook_info->getResult();
                        if ($result->url === Yii::$app->user->webhookUrl) {
                            echo Html::a('☹️ Оптисать бота', ['/user/profile/unset'], ['class' => 'btn btn-sm btn-danger']);
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="card-body">

                <?php
                if ($me->isOk()) {
                    $result = $me->getResult();
                    $profile = Request::getUserProfilePhotos(['user_id' => $result->id]); //812367093 me

                    if ($profile->getResult()->photos) {
                        $photo = $profile->getResult()->photos[0][2];
                        $file = Request::getFile(['file_id' => $photo['file_id']]);
                        if (!file_exists(Yii::getAlias('@app/web/telegram/downloads') . DIRECTORY_SEPARATOR . $file->getResult()->file_path)) {
                            $download = Request::downloadFile($file->getResult());

                        } else {
                            echo Html::img('/telegram/downloads/' . $file->getResult()->file_path, ['class' => 'mb-4', 'width' => 100]);
                        }
                    }
                    ?>
                <?php } ?>

                <?php if ($model->expire) { ?>
                    <div class="form-group row">
                        <div class="col-sm-5 col-lg-5"><label>Продлен до</label></div>
                        <div class="col-sm-7 col-lg-7">
                            <?= \panix\engine\CMS::date($model->expire); ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($model->plan_id) { ?>
                    <div class="form-group row">
                        <div class="col-sm-5 col-lg-5"><label>Текущий тариф</label></div>
                        <div class="col-sm-7 col-lg-7">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?= Yii::$app->params['plan'][$model->plan_id]['name']; ?>
                                    <?php if ($model->trial) {
                                        echo Html::tag('span', 'TRIAL', ['class' => 'badge badge-danger']);
                                    }
                                    ?>
                                </div>
                                <div class="col-lg-6 text-lg-right"><?= Html::a('Оплатить', '', ['class' => 'btn btn-success']); ?></div>
                            </div>

                        </div>
                    </div>
                <?php } ?>


                <div class="table-responsive">
                    <table class="table table-striped">
                        <?php if ($model->api_key) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('api_key'); ?></th>
                                <td style="width: 70%"><code><?= $model->api_key; ?></code></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->username) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('username'); ?></th>
                                <td style="width: 70%"><?= $model->username; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->create_ip) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('create_ip'); ?></th>
                                <td style="width: 70%"><?= $model->create_ip; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->login_time) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('login_time'); ?></th>
                                <td style="width: 70%"><?= $model->login_time; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->login_ip) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('login_ip'); ?></th>
                                <td style="width: 70%"><?= CMS::ip($model->login_ip); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->created_at) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('created_at'); ?></th>
                                <td style="width: 70%"><?= CMS::date($model->created_at); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->updated_at) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('updated_at'); ?></th>
                                <td style="width: 70%"><?= CMS::date($model->updated_at); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($model->login_user_agent) { ?>
                            <tr>
                                <th style="width: 30%"><?= $model->getAttributeLabel('login_user_agent'); ?></th>
                                <td style="width: 70%"><?= new \panix\engine\components\Browser($model->login_user_agent); ?></td>
                            </tr>
                        <?php } ?>

                    </table>

                </div>

            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'reset-form',
            'fieldConfig' => [
                'template' => "<div class=\"col-sm-6 col-lg-6\">{label}</div>\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-form-label',
                    'offset' => 'offset-sm-6 offset-lg-6',
                    'wrapper' => 'col-sm-6 col-lg-6',
                    'error' => '',
                    'hint' => '',
                ],
                // 'labelOptions' => ['class' => 'col-lg-22 control-label'],
            ],
        ]); ?>


        <div class="card">
            <div class="card-header">
                <?= Yii::t('user/default', 'CHANGE_PASSWORD'); ?>
            </div>
            <div class="card-body">
                <?= $form->field($changePasswordForm, 'current_password')->passwordInput() ?>
                <?= $form->field($changePasswordForm, 'new_password')->passwordInput() ?>
                <?= $form->field($changePasswordForm, 'password_confirm')->passwordInput() ?>

            </div>
            <div class="card-footer text-center">
                <?= Html::submitButton(Yii::t('app/default', 'UPDATE'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
