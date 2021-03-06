<?php

use panix\engine\Html;
use yii\widgets\Breadcrumbs;

$asset = \core\web\themes\dashboard\AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Yii::$app->charset; ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title><?= Yii::t('app/admin', 'ADMIN_PANEL'); ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

    <header class="topbar" data-navbarbg="skin6">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-header" data-logobg="skin5">
                <!-- This is for the sidebar toggle which is visible on mobile only -->
                <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                    <i class="icon-menu"></i>
                </a>

                <div class="navbar-brand">
                    <a href="/" class="logo">
                        <b class="logo-icon">
                            <img src="<?= $asset->baseUrl; ?>/images/logo.svg" alt="homepage"
                                 class="light-logo"/>
                        </b>
                    </a>
                    <a class="sidebartoggler d-none d-md-block" href="javascript:void(0)"
                       data-sidebartype="mini-sidebar">
                        <i class="icon-menu"></i>
                    </a>
                </div>

                <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                   data-toggle="collapse" data-target="#navbarSupportedContent"
                   aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="icon-menu"></i>
                </a>
            </div>

            <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin6">
                <?= $this->render('partials/_navbar',['asset'=>$asset]); ?>
            </div>
        </nav>
    </header>

    <aside class="left-sidebar" data-sidebarbg="skin5">
        <div class="scroll-sidebar ps-container ps-theme-default ps-active-y">
            <?= $this->render('partials/_sidebar'); ?>
        </div>
    </aside>
    <div class="page-wrapper">

            <?= $content; ?>

        <footer class="footer text-center">
            &copy; 2019-<?= date('Y');?> &laquo;<?= Html::a('ShopiumBot',['https://shopiumbot.com']); ?>&raquo;
        </footer>

    </div>

</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
