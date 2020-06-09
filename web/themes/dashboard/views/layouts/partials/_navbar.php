<?php
use panix\engine\Html;
use yii\helpers\Url;
use panix\engine\CMS;

$api = new \shopium\mod\telegram\components\Api;
$bot = \shopium\mod\telegram\models\User::find()->where(['id'=>$api->getBotId()])->one();
$botImage = $bot->getPhoto();

?>
<ul class="navbar-nav float-left mr-auto">
    <!-- <li class="nav-item d-none d-md-block">
        <a class="nav-link sidebartoggler" href="javascript:void(0)" data-sidebartype="mini-sidebar">
            <i class="mdi mdi-menu font-24"></i>
        </a>
    </li> -->
    <!-- ============================================================== -->
    <!-- Search -->
    <!-- ============================================================== -->
    <li class="nav-item search-box d-none">
        <a class="nav-link" href="javascript:void(0)">
            <div class="d-flex align-items-center">
                <i class="icon-search font-20 mr-1"></i>
                <div class="ml-1 d-none d-sm-block">
                    <span>Поиск</span>
                </div>
            </div>
        </a>
        <form class="app-search position-absolute">
            <input type="text" class="form-control" placeholder="Search &amp; enter">
            <a class="srh-btn">
                <i class="icon-delete"></i>
            </a>
        </form>
    </li>
</ul>
<ul class="navbar-nav float-right">
    <?php

    $orders = \shopium\mod\cart\models\Order::find()->where('status_id')->all();
    if($orders){
    ?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="icon-cart"></i>
            <span class="badge badge-pill badge-success"><?= Yii::$app->getModule('cart')->count['num']; ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown" aria-labelledby="2">
            <span class="with-arrow"><span class="bg-danger"></span></span>
            <ul class="list-style-none">
                <li>
                    <div class="drop-title text-white bg-danger">
                        <h4 class=""><?= ucfirst(Yii::t('cart/default','ORDERS_COUNTER',Yii::$app->getModule('cart')->count['num']));?></h4>
                        <span class="font-light"></span>
                    </div>
                </li>
                <li>
                    <div class="message-center message-body">
                        <?php

                        foreach ($orders as $order){

            //                \panix\engine\CMS::dump($order->user->getPhoto());
                        ?>
                            <a href="<?= Url::to(['/admin/cart/default/update','id'=>$order->id]);?>" class="message-item">
                                                <span class="user-img">
                                                    <img src="<?= $order->user->getPhoto(); ?>" alt="<?= $order->firstname; ?> <?= $order->lastname; ?>"
                                                         class="rounded-circle">
                                                    <span class="profile-status online pull-right"></span>
                                                </span>
                                <div class="mail-contnet">
                                    <h5 class="message-title"><?= $order->firstname; ?> <?= $order->lastname; ?></h5>
                                    <span class="mail-desc"><?= Yii::t('shop/default','PRODUCTS_COUNTER',$order->productsCount);?></span>
                                    <span class="time"><?= Yii::$app->currency->number_format($order->total_price); ?> грн</span>
                                </div>
                            </a>
                        <?php } ?>


                    </div>
                </li>
                <li>
                    <?= Html::a(Html::icon('arrow-right').' <string>Все заказы</string>',['/admin/cart'],['class'=>'nav-link text-center link text-dark']); ?>
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
        <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown">
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
            <img src="<?= $botImage; ?>" alt="<?= $bot->username; ?>" class="rounded-circle"
                 width="40">
            <span class="m-l-5 font-medium d-none d-sm-inline-block"><?= Yii::$app->user->getDisplayName(); ?> <i
                        class="icon-arrow-down"></i></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow">
                                    <span class="bg-primary"></span>
                                </span>
            <div class="d-flex no-block align-items-center p-2 bg-primary text-white mb-3">
                <div class="">
                    <img src="<?= $botImage; ?>" alt="<?= $bot->username; ?>" class="rounded-circle"
                         width="60">
                </div>
                <div class="ml-2">
                    <h5 class="mb-0"><?= Yii::$app->user->getDisplayName(); ?></h5>
                    <p class="mb-0"><?= $bot->username; ?></a>
                    </p>
                </div>
            </div>
            <div class="profile-dis scrollable">
                <a class="dropdown-item d-none" href="/profile">
                    <i class="icon-user-outline mr-1 ml-1"></i> Аккаунт</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/logout">
                    <i class="icon-logout mr-1 ml-1"></i> Выход</a>
                <div class="dropdown-divider"></div>
            </div>
            <div class="pl-3 p-2 d-none">
                <a href="javascript:void(0)" class="btn btn-sm btn-success btn-rounded">View Profile</a>
            </div>
        </div>
    </li>
    <!-- ============================================================== -->
    <!-- User profile and search -->
    <!-- ============================================================== -->
</ul>