<?php

use panix\engine\Html;
use yii\widgets\Breadcrumbs;

$asset = \core\web\themes\dashboard\AdminAsset::register($this);
//echo Yii::$app->security->generatePasswordHash('');
// LWS199812204510
// my_brand_shoes_18
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


<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->
<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar" data-navbarbg="skin6">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-header" data-logobg="skin5">
                <!-- This is for the sidebar toggle which is visible on mobile only -->
                <a class="nav-toggler d-block d-md-none" href="javascript:void(0)">
                    <i class="icon-menu"></i>
                </a>
                <div class="navbar-brand">
                    <a href="/" class="logo">
                        <!-- Logo icon -->
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
                <a class="topbartoggler d-block d-md-none" href="javascript:void(0)"
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
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-5 align-self-center">
                    <h4 class="page-title"><?= $this->context->pageName; ?></h4>
                </div>
                <div class="col-7 align-self-center">
                    <div class="d-flex align-items-center justify-content-end">
                        <?php
                        if (!isset($this->context->buttons)) {
                            if (method_exists($this->context, 'actionCreate')) {
                                echo Html::a(Yii::t('app', 'CREATE'), ['create'], ['title' => Yii::t('app', 'CREATE'), 'class' => 'btn btn-success']);
                            }
                        } else {
                            if ($this->context->buttons == true) {
                                if (is_array($this->context->buttons)) {

                                    if (count($this->context->buttons) > 1) {
                                        echo Html::beginTag('div', ['class' => 'btn-group']);
                                    }
                                    foreach ($this->context->buttons as $button) {
                                        if (isset($button['icon'])) {
                                            $icon = Html::icon($button['icon']) . ' ';
                                        } else {
                                            $icon = '';
                                        }
                                        if (!isset($button['options']['class'])) {
                                            $button['options']['class'] = ['btn btn-secondary'];
                                        }
                                        if (!empty($icon))
                                            $button['label'] = '<span class="d-none d-sm-inline">' . $button['label'] . '</span>';

                                        if (empty($icon))
                                            $button['options']['title'] = $button['label'];

                                        echo Html::a($icon . $button['label'], $button['url'], $button['options']);
                                    }
                                    if (count($this->context->buttons) > 1) {
                                        echo Html::endTag('div');
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">






            <?= $content; ?>

        </div>

        <footer class="footer text-center">
            &copy; 2019-<?= date('Y');?> &laquo;<?= Html::a('ShopiumBot',['https://shopiumbot.com']); ?>&raquo;
        </footer>

    </div>

</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
