<?php
use panix\engine\Html;
use yii\helpers\Url;
use panix\engine\CMS;


$telegram = Yii::$app->telegram;

?>
<ul class="navbar-nav float-left mr-auto">
    <!-- <li class="nav-item d-none d-md-block">
        <a class="nav-link sidebartoggler" href="javascript:void(0)" data-sidebartype="mini-sidebar">
            <i class="mdi mdi-menu font-24"></i>
        </a>
    </li> -->
    <li class="nav-item search-box">
        <a class="nav-link" href="javascript:void(0)">
            <div class="d-flex align-items-center">
                <i class="icon-search font-20 mr-1"></i>
                <div class="ml-1 d-none d-sm-block">
                    <span><?= Yii::t('default','SEARCH_PRODUCT'); ?></span>
                </div>
            </div>
        </a>
        <form method="GET" action="<?= Url::to(['/admin/shop/product']); ?>" class="app-search position-absolute">
            <input name="ProductSearch[search_string]" type="text" class="form-control"
                   placeholder="<?= Yii::t('default','SEARCH_PLACEHOLDER'); ?>">
            <a class="search-submit-btn d-none">
                <i class="icon-search"></i>
            </a>
            <a class="search-close-btn">
                <i class="icon-delete"></i>
            </a>
        </form>
    </li>
</ul>
<ul class="navbar-nav float-right">
    <?php
    \panix\engine\emoji\EmojiAsset::register($this);
    $emoji = new \panix\engine\emoji\Emoji;
    ?>
    <?php if (count(Yii::$app->languageManager->getLanguages()) > 1) { ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $emoji->emoji_unified_to_html(Yii::$app->languageManager->active['icon']); ?>
                <span class="text-uppercase ml-2"><?= Yii::$app->languageManager->active['code']; ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right animate__animated animate__bounceInDown" aria-labelledby="2">
                <span class="with-arrow"><span class=""></span></span>

                <?php
                foreach (Yii::$app->languageManager->getLanguages() as $lang) {
                    $active = ($lang->code == Yii::$app->language) ? $lang->code . ' active' : $lang->code;
                   // $link = ($lang->is_default) ? CMS::currentUrl() : '/' . $lang->code . CMS::currentUrl();
                    ?>
                    <?php
                    echo Html::a($emoji->emoji_unified_to_html($lang->icon) . ' <span class="ml-2">' . $lang->name . '</span>',['/admin/app/default/set-language','lang'=>$lang->code,'redirect'=>Yii::$app->request->url], ['class' => $active . ' dropdown-item  d-flex align-items-center']);
                    ?>
                    <?php // Html::a($emoji->emoji_unified_to_html($lang->icon) . ' <span class="ml-2">' . $lang->name . '</span>', $link, ['class' => $active . ' dropdown-item  d-flex align-items-center']); ?>
                <?php } ?>

            </div>
        </li>
    <?php } ?>


    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="icon-cash-money d-none d-md-inline"></i>
            <span class="ml-2"><strong><?= Yii::$app->currency->number_format(Yii::$app->user->money); ?></strong> <small>UAH</small></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right mailbox animate__animated animate__bounceInDown"
             aria-labelledby="2">
            <span class="with-arrow"><span class="bg-danger"></span></span>
            <ul class="list-style-none">
                <li>
                    <div class="drop-title text-white bg-danger">
                        <h4 class="">Операции</h4>
                        <span class="font-light"></span>
                    </div>
                </li>
                <li>
                    <div class="message-center message-body">
                        <?php
                        $payments = \core\modules\user\models\Payments::find()
                            ->where(['user_id' => Yii::$app->user->id])
                            ->orderBy(['id' => SORT_DESC])
                            ->all();
                        foreach ($payments as $payment) {
                            ?>
                            <div class="message-item">

                                <h5 class="message-title">#<?= $payment->id; ?>
                                    <?php if ($payment->status == 'success') { ?>
                                        <span class="badge badge-success">Success</span>
                                    <?php } else { ?>
                                        <?php

                                        if ($payment->data && !empty($payment->data)) {
                                            $data = json_decode($payment->data);
                                            $status = $data->err_code;
                                        } else {
                                            $status = $payment->status;
                                        }

                                        ?>
                                        <span class="badge badge-danger"><?= $status; ?></span>
                                    <?php } ?>
                                </h5>
                                <span class="mail-desc">
                                    
                                    <?= $payment->name; ?>

                                </span>
                                <div class="time"><strong><?= $payment->money; ?></strong>
                                    <small>UAH</small>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                </li>
            </ul>
        </div>
    </li>
    <?php

    $orders = \shopium\mod\cart\models\Order::find()->where(['status_id' => 1])->all();
    if ($orders) {
        ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-cart"></i>
                <span class="badge badge-pill badge-success"><?= Yii::$app->getModule('cart')->count['num']; ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right mailbox animate__animated animate__bounceInDown"
                 aria-labelledby="2">
                <span class="with-arrow"><span class="bg-danger"></span></span>
                <ul class="list-style-none">
                    <li>
                        <div class="drop-title text-white bg-danger">
                            <h4 class=""><?= ucfirst(Yii::t('cart/default', 'ORDERS_COUNTER', Yii::$app->getModule('cart')->count['num'])); ?></h4>
                            <span class="font-light"></span>
                        </div>
                    </li>
                    <li>
                        <div class="message-center message-body">
                            <?php

                            foreach ($orders as $order) {

                                //\panix\engine\CMS::dump($order->user->getPhoto());
                                ?>
                                <a href="<?= Url::to(['/admin/cart/default/update', 'id' => $order->id]); ?>"
                                   class="message-item">
                                                <span class="user-img">
                                                    <img src="<?= $order->user->getPhoto(); ?>"
                                                         alt="<?= $order->firstname; ?> <?= $order->lastname; ?>"
                                                         class="rounded-circle">
                                                    <span class="profile-status online pull-right d-none"></span>
                                                </span>

                                    <div class="mail-content">
                                        <h5 class="message-title"><?= $order->firstname; ?> <?= $order->lastname; ?></h5>
                                        <span class="mail-desc"><?= ($order->created_at) ? CMS::date($order->created_at) : ''; ?></span>
                                        <span class="time"><?= Yii::t('shop/default', 'PRODUCTS_COUNTER', $order->productsCount); ?>
                                            / <strong><?= Yii::$app->currency->number_format($order->total_price); ?></strong> UAH</span>
                                    </div>
                                </a>
                            <?php } ?>


                        </div>
                    </li>
                    <li>
                        <?= Html::a(Html::icon('arrow-right') . ' <string>'.Yii::t('default','ALL_ORDERS').'</string>', ['/cart'], ['class' => 'nav-link text-center link text-dark']); ?>
                    </li>
                </ul>
            </div>
        </li>
    <?php } ?>
    <li class="nav-item dropdown border-right  d-none">
        <a class="nav-link dropdown-toggle" href="" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false">
            <i class="icon-notification-outline"></i>
            <span class="badge badge-pill badge-info">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right mailbox animate__animated animate__bounceInDown">
                                <span class="with-arrow">
                                    <span class="bg-primary"></span>
                                </span>
            <ul class="list-style-none">
                <li>
                    <div class="drop-title bg-primary text-white">
                        <h4 class="">4 New</h4>
                        <span class="font-light">Notifications</span>
                    </div>
                </li>
                <li>
                    <div class="message-center notifications">
                        <!-- Message -->
                        <a href="javascript:void(0)" class="message-item">
                                                <span class="btn btn-danger btn-circle">
                                                    <i class="icon-external-link"></i>
                                                </span>
                            <div class="mail-contnet">
                                <h5 class="message-title">Luanch Admin</h5>
                                <span class="mail-desc">Just see the my new admin!</span>
                                <span class="time">9:30 AM</span>
                            </div>
                        </a>
                        <!-- Message -->
                        <a href="javascript:void(0)" class="message-item">
                                                <span class="btn btn-success btn-circle">
                                                    <i class="icon-calendar"></i>
                                                </span>
                            <div class="mail-contnet">
                                <h5 class="message-title">Event today</h5>
                                <span class="mail-desc">Just a reminder that you have event</span>
                                <span class="time">9:10 AM</span>
                            </div>
                        </a>
                        <!-- Message -->
                        <a href="javascript:void(0)" class="message-item">
                                                <span class="btn btn-info btn-circle">
                                                    <i class="icon-settings"></i>
                                                </span>
                            <div class="mail-contnet">
                                <h5 class="message-title">Settings</h5>
                                <span class="mail-desc">You can customize this template as you want</span>
                                <span class="time">9:08 AM</span>
                            </div>
                        </a>
                        <!-- Message -->
                        <a href="javascript:void(0)" class="message-item">
                                                <span class="btn btn-primary btn-circle">
                                                    <i class="icon-user"></i>
                                                </span>
                            <div class="mail-contnet">
                                <h5 class="message-title">Pavan kumar</h5>
                                <span class="mail-desc">Just see the my admin!</span>
                                <span class="time">9:02 AM</span>
                            </div>
                        </a>
                    </div>
                </li>
                <li>
                    <a class="nav-link text-center m-b-5 text-dark" href="javascript:void(0);">
                        <strong>Check all notifications</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle pro-pic" href=""
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="<?= $telegram->getPhoto(); ?>" alt="<?= $telegram->getApi()->getBotUsername(); ?>"
                 class="rounded-circle"
                 width="40">
            <span class="m-l-5 font-medium d-none d-sm-inline-block"><?= Yii::$app->user->getDisplayName(); ?> <i
                        class="icon-arrow-down"></i></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right user-dd animate__animated animate__flipInY">
                                <span class="with-arrow">
                                    <span class="bg-primary"></span>
                                </span>
            <div class="d-flex no-block align-items-center p-2 bg-primary text-white mb-3">
                <div class="">
                    <img src="<?= $telegram->getPhoto(); ?>" alt="<?= $telegram->getApi()->getBotUsername(); ?>"
                         class="rounded-circle"
                         width="60">
                </div>
                <div class="ml-2">
                    <h5 class="mb-0"><?= Yii::$app->user->getDisplayName(); ?></h5>
                    <p class="mb-0"><?= Yii::t('default','BALANCE'); ?> <?= Yii::$app->user->money; ?> UAH</p>
                    <p class="mb-0 d-none"><?= $telegram->getApi()->getBotUsername(); ?></p>
                </div>
            </div>
            <div class="profile-dis scrollable">
                <?= Html::a('<i class="icon-user-outline mr-1 ml-1"></i> '.Yii::t('user/default','ACCOUNT'), ['/user/index'], ['class' => 'dropdown-item']); ?>
                <?= Html::a('<i class="icon-cash-money mr-1 ml-1"></i> '.Yii::t('default','MY_PAYMENTS'), ['/user/payments'], ['class' => 'dropdown-item d-none']); ?>
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="<?= Url::to(['/user/default/logout']); ?>">
                    <i class="icon-logout mr-1 ml-1"></i> <?= Yii::t('user/default','LOGOUT');?></a>
                <div class="dropdown-divider"></div>
            </div>
            <div class="pl-3 p-2 d-none">
                <a href="javascript:void(0)" class="btn btn-sm btn-success btn-rounded">View Profile</a>
            </div>
        </div>
    </li>

</ul>