<?php

namespace core\modules\shop\blocks\popular;

use core\modules\shop\models\Product;
use panix\engine\data\ActiveDataProvider;
use panix\engine\data\Widget;

class PopularBlock extends Widget
{

    public $limiter = 10;

    public function run()
    {

        $query = Product::find();
        $query->limit($this->limiter);
        $query->orderBy('views');
        //$query->joinWith('translations');
        //$query->with('translations');

        $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => Product::getSort(),
                'pagination' => false
            ]
        );

        return $this->render($this->skin, ['dataProvider' => $dataProvider]);
    }

}
