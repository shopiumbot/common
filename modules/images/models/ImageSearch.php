<?php

namespace core\modules\images\models;

use Yii;
use yii\base\Model;
use panix\engine\data\ActiveDataProvider;

/**
 * PagesSearch represents the model behind the search form about `app\modules\pages\models\Pages`.
 */
class ImageSearch extends Image
{

    public $exclude = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param array $configure
     *
     * @return ActiveDataProvider
     */
    public function search($params, $configure = [])
    {
        $query = Image::find();

        $query->orderBy(['ordern' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => self::getSort()
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->where([
            'product_id' => $configure['model']->primaryKey,
        ]);

        //$query->andFilterWhere(['id' => $this->id]);
        // Id of product to exclude from search
        if ($this->exclude) {
            //foreach ($this->exclude as $id) {
            //    $query->andFilterWhere(['!=', '{{%shop_product}}.id', $id]);
            //}
        }


       // echo $query->createCommand()->rawSql;die;
        return $dataProvider;
    }

}
