<?php
use yii\helpers\FileHelper;

$products = \core\modules\shop\models\Product::find()->all();
foreach ($products as $product) {
    $images = $product->getImages();
    echo $product->id . ': ';
    if (file_exists(Yii::getAlias('@uploads/store/product/') . $product->id)) {
        $files = FileHelper::findFiles(Yii::getAlias('@uploads/store/product/') . $product->id);

        if (count($files) > 1) {
            foreach ($files as $file) {
                echo basename($file);
                $img = $product->getImages(['filePath' => basename($file)]);
                if (!$img) {
                    unlink($file);
                }
            }

        }
    }

    if (count($images) > 1) {

        foreach ($images as $ss) {
            if (!$ss->is_main) {
                echo $ss->filePath . ',';
                //	$product->removeImage($ss);
            }
        }

        echo '<br>';
    }
}