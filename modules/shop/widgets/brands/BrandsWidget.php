<?php

namespace app\modules\shop\widgets\brands;

use panix\engine\data\Widget;
use panix\ext\owlcarousel\OwlCarouselAsset;
use app\modules\shop\models\Manufacturer;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class BrandsWidget extends Widget
{

    public function run()
    {
        $model = Manufacturer::find()
            ->published()
            ->isNotEmpty('image')
            ->all();
        return $this->render($this->skin, ['model' => $model]);
    }

}
