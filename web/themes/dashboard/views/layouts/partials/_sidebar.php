<?php
use panix\engine\Html;

?>

<nav class="sidebar-nav">
    <ul id="sidebarnav">
        <li class="nav-small-cap">
            <i class="icon-telegram-outline"></i>
            <span class="hide-menu"><?= Yii::t('default','TELEGRAM_STORE'); ?></span>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('folder-open') . '<span class="hide-menu">'.Yii::t('shop/default','CATALOG').'</span>', ['/shop/category'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('shopcart') . '<span class="hide-menu">'.Yii::t('shop/admin','PRODUCTS').'</span>', ['/shop/product'], ['class' => 'sidebar-link']); ?>
        </li>
        <?php if (in_array(Yii::$app->user->planId, [2, 3, 4])) { ?>
            <li class="sidebar-item">
                <?= Html::a(Html::icon('discount') . '<span class="hide-menu">'.Yii::t('discounts/default','MODULE_NAME').'</span>', ['/discounts/default/index'], ['class' => 'sidebar-link']); ?>
            </li>
        <?php } ?>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('apple') . '<span class="hide-menu">'.Yii::t('shop/default','MANUFACTURERS').'</span>', ['/shop/manufacturer'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('t') . '<span class="hide-menu">'.Yii::t('shop/admin','TYPE_PRODUCTS').'</span>', ['/shop/type'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('filter') . '<span class="hide-menu">'.Yii::t('shop/admin','ATTRIBUTES').'</span>', ['/shop/attribute'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('currencies') . '<span class="hide-menu">'.Yii::t('shop/admin','CURRENCY').'</span>', ['/shop/currency'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="javascript:void(0)"
               aria-expanded="false">
                <i class="icon-cart"></i>
                <span class="hide-menu"><?= Yii::t('cart/admin','ORDERS'); ?></span>
                <span class="badge badge-pill badge-success ml-auto mr-3"><?= Yii::$app->getModule('cart')->count['num']; ?></span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('cart') . '<span class="hide-menu">'.Yii::t('cart/admin','ORDERS_LIST').'</span>', ['/cart/default/index'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('delivery') . '<span class="hide-menu">'.Yii::t('cart/admin','DELIVERY').'</span>', ['/cart/delivery/index'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('creditcard') . '<span class="hide-menu">'.Yii::t('cart/admin','PAYMENTS').'</span>', ['/cart/payment/index'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('stats') . '<span class="hide-menu">'.Yii::t('cart/admin','STATUSES').'</span>', ['/cart/statuses/index'], ['class' => 'sidebar-link']); ?>
                </li>
            </ul>
        </li>


        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="javascript:void(0)"
               aria-expanded="false">
                <i class="icon-telegram-outline"></i>
                <span class="hide-menu">Telegram</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('comments') . '<span class="hide-menu">'.Yii::t('telegram/default', 'MESSAGES').'</span>', ['/telegram/message'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('messages') . '<span class="hide-menu">'.Yii::t('telegram/default', 'MAILING').'</span>', ['/telegram/mailing'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('user-outline') . '<span class="hide-menu">'.Yii::t('telegram/default', 'USERS').'</span>', ['/telegram/users'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('arrow-right') . '<span class="hide-menu">'.Yii::t('telegram/default', 'START_SOURCE').'</span>', ['/telegram/start-source'], ['class' => 'sidebar-link']); ?>
                </li>
            </ul>
        </li>

        <li class="nav-small-cap">
            <i class="icon-puzzle"></i>
            <span class="hide-menu"><?= Yii::t('default','INTEGRATION'); ?></span>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('arrow-right') . '<span class="hide-menu">PROM.UA</span>', ['/admin/promua'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('novaposhta') . '<span class="hide-menu">Нова Пошта</span>', ['/admin'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow " href="javascript:void(0)"
               aria-expanded="false">
                <i class="icon-upload"></i>
                <span class="hide-menu"><?= Yii::t('csv/default', 'MODULE_NAME'); ?></span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('file-csv') . '<span class="hide-menu">'.Yii::t('csv/default','IMPORT').'</span>', ['/csv/default/import'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('file-csv') . '<span class="hide-menu">'.Yii::t('csv/default','EXPORT').'</span>', ['/csv/default/export'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('settings') . '<span class="hide-menu">'.Yii::t('app/default','SETTINGS').'</span>', ['/csv/settings/index'], ['class' => 'sidebar-link']); ?>
                </li>
            </ul>
        </li>
        <?php if (YII_DEBUG) { ?>
            <li class="sidebar-item">
                <a class="sidebar-link has-arrow " href="javascript:void(0)"
                   aria-expanded="false">
                    <i class="icon-upload"></i>
                    <span class="hide-menu">YML - формат</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                    <li class="sidebar-item">
                        <?= Html::a(Html::icon('file') . '<span class="hide-menu">'.Yii::t('csv/default','IMPORT').'</span>', ['/yml/default/import'], ['class' => 'sidebar-link']); ?>
                    </li>
                    <li class="sidebar-item">
                        <?= Html::a(Html::icon('file') . '<span class="hide-menu">'.Yii::t('csv/default','EXPORT').'</span>', ['/yml/default/export'], ['class' => 'sidebar-link']); ?>
                    </li>
                </ul>
            </li>
        <?php } ?>


        <li class="nav-small-cap">
            <i class="icon-books"></i>
            <span class="hide-menu"><?= Yii::t('default','ADDITIONAL'); ?></span>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('edit') . '<span class="hide-menu">'.Yii::t('menu/default', 'MODULE_NAME').'</span>', ['/menu/default/index'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('language') . '<span class="hide-menu">'.Yii::t('admin/default','LANGUAGES').'</span>', ['/admin/app/languages'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('settings') . '<span class="hide-menu">'.Yii::t('app/default','SETTINGS').'</span>', ['/admin/app/settings'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('phone-outline') . '<span class="hide-menu">'.Yii::t('contacts/default','MODULE_NAME').'</span>', ['/contacts'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('info') . '<span class="hide-menu">Документация</span>', ['/documentation'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('tools') . '<span class="hide-menu">API</span>', 'https://shopiumbot.docs.apiary.io', ['class' => 'sidebar-link', 'target' => '_blank']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('logout') . '<span class="hide-menu">'.Yii::t('user/default','LOGOUT').'</span>', ['/site/logout'], ['class' => 'sidebar-link']); ?>
        </li>

    </ul>
</nav>