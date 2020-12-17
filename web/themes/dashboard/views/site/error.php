<?php
use yii\helpers\Html;

/**
 * @var $exception \yii\web\HttpException
 * @var $handler \yii\web\ErrorHandler
 */


?>

<div class="row">
    <div class="col-12">
        <div class="text-center">
            <h1 class="error-logo"><?= $statusCode; ?></h1>
            <h2><?= $exception->getMessage(); ?></h2>
            <p>
                <?= Html::a(Yii::t('app/default', 'GO_HOME'), ['/admin'], ['class' => 'btn btn-lg btn-primary mt-5']); ?>
            </p>
        </div>
    </div>
    <?php if (YII_DEBUG) { ?>
        <div class="col-12">
            <?php foreach ($exception->getTrace() as $trace) { ?>
                <div style="word-break: break-all;">
                    <div><strong><?= $trace['file'] ?></strong>(<?= $trace['line'] ?>)</div>
                    <div class="help-block text-muted"><?= $trace['class'] ?><?= $trace['type'] ?><?= $trace['function'] ?></div>
                    <hr/>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>