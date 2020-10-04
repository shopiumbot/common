<?php
use panix\engine\Html;

?>

<nav class="sidebar-nav">
    <ul id="sidebarnav">
        <li class="nav-small-cap">
            <i class="icon-telegram-outline"></i>
            <span class="hide-menu">Telegram-магазин</span>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('folder-open') . '<span class="hide-menu">Каталог</span>', ['/admin/shop/category'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('shopcart') . '<span class="hide-menu">Продукция</span>', ['/admin/shop/product'], ['class' => 'sidebar-link']); ?>
        </li>
        <?php if (in_array(Yii::$app->user->planId, [2, 3, 4])) { ?>
            <li class="sidebar-item">
                <?= Html::a(Html::icon('discount') . '<span class="hide-menu">Скидки</span>', ['/discounts/admin/default/index'], ['class' => 'sidebar-link']); ?>
            </li>
        <?php } ?>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('apple') . '<span class="hide-menu">Бренды</span>', ['/admin/shop/manufacturer'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('t') . '<span class="hide-menu">Типы товаров</span>', ['/admin/shop/type'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('filter') . '<span class="hide-menu">Атрибуты</span>', ['/admin/shop/attribute'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('currencies') . '<span class="hide-menu">Валюты</span>', ['/admin/shop/currency'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="javascript:void(0)"
               aria-expanded="false">
                <i class="icon-cart"></i>
                <span class="hide-menu">Заказы</span>
                <span class="badge badge-pill badge-success ml-auto mr-3"><?= Yii::$app->getModule('cart')->count['num']; ?></span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('cart') . '<span class="hide-menu">Список заказов</span>', ['/admin/cart/default/index'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('delivery') . '<span class="hide-menu">Доставка</span>', ['/admin/cart/delivery/index'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('creditcard') . '<span class="hide-menu">Оплата</span>', ['/admin/cart/payment/index'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('stats') . '<span class="hide-menu">Статусы</span>', ['/admin/cart/statuses/index'], ['class' => 'sidebar-link']); ?>
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
                    <?= Html::a(Html::icon('comments') . '<span class="hide-menu">Сообщения</span>', ['/telegram/message'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('messages') . '<span class="hide-menu">Рассылка</span>', ['/telegram/mailing'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('user-outline') . '<span class="hide-menu">Пользователи</span>', ['/telegram/users'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('arrow-right') . '<span class="hide-menu">Источники входа</span>', ['/telegram/start-source'], ['class' => 'sidebar-link']); ?>
                </li>
            </ul>
        </li>

        <li class="nav-small-cap">
            <i class="icon-puzzle"></i>
            <span class="hide-menu">Интеграция</span>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('arrow-right') . '<span class="hide-menu">PROM.UA</span>', ['/admin/promua'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('novaposhta') . '<span class="hide-menu">Нова Пошта</span>', ['/admin/promua'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow " href="javascript:void(0)"
               aria-expanded="false">
                <i class="icon-upload"></i>
                <span class="hide-menu">csv,xls,xlsx - формат</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('file-csv') . '<span class="hide-menu">Импорт</span>', ['/csv/default/import'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('file-csv') . '<span class="hide-menu">Экспорт</span>', ['/csv/default/export'], ['class' => 'sidebar-link']); ?>
                </li>
                <li class="sidebar-item">
                    <?= Html::a(Html::icon('settings') . '<span class="hide-menu">Настройки</span>', ['/csv/settings/index'], ['class' => 'sidebar-link']); ?>
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
                        <?= Html::a(Html::icon('file') . '<span class="hide-menu">Импорт</span>', ['/yml/default/import'], ['class' => 'sidebar-link']); ?>
                    </li>
                    <li class="sidebar-item">
                        <?= Html::a(Html::icon('file') . '<span class="hide-menu">Экспорт</span>', ['/yml/default/export'], ['class' => 'sidebar-link']); ?>
                    </li>
                </ul>
            </li>
        <?php } ?>


        <li class="nav-small-cap">
            <i class="icon-books"></i>
            <span class="hide-menu">Дополнительный</span>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('edit') . '<span class="hide-menu">Меню</span>', ['/pages/default/index'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('settings') . '<span class="hide-menu">Настройки</span>', ['/admin/app/settings'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('phone-outline') . '<span class="hide-menu">Контакты</span>', ['/contacts'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('info') . '<span class="hide-menu">Документация</span>', ['/documentation'], ['class' => 'sidebar-link']); ?>
        </li>
        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('tools') . '<span class="hide-menu">API</span>', 'https://shopiumbot.docs.apiary.io', ['class' => 'sidebar-link', 'target' => '_blank']); ?>
        </li>
        <li class="sidebar-item">
            <?= Html::a(Html::icon('logout') . '<span class="hide-menu">Выход</span>', ['/site/logout'], ['class' => 'sidebar-link']); ?>
        </li>

        <li class="sidebar-item d-none">
            <?= Html::a(Html::icon('phone') . '<span class="hide-menu">contacts</span>', ['/contacts'], ['class' => 'sidebar-link']); ?>
        </li>

    </ul>
</nav>