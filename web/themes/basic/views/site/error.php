<?php

use panix\engine\Html;

/**
 * @var $exception \yii\web\HttpException
 * @var $handler \yii\web\ErrorHandler
 */

?>


    <div>
        <div class="text-center">
            <div class="heading-gradient text-center">
                <h1><?= $exception->statusCode; ?></h1>
            </div>
            <p class="lead">
                <strong><?= $exception->getMessage() ?></strong>
            </p>
        </div>
    </div>


<?php if (YII_DEBUG) { ?>
    <h2 class="text-center">Trace</h2>
    <?php foreach ($exception->getTrace() as $index => $trace) { ?>
        <div style="word-break: break-all;">
            <div><strong><?= $trace['file']; ?></strong> (<?= $trace['line'] ?>)</div>
            <div class="help-block text-muted"><?= $trace['class'] ?><?= $trace['type'] ?><?= $trace['function'] ?></div>
            <hr/>
        </div>
    <?php } ?>
<?php } ?>