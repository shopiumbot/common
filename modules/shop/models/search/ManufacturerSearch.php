<?php

namespace app\modules\shop\models\search;

use Yii;
use yii\base\Model;
use panix\engine\data\ActiveDataProvider;
use app\modules\shop\models\Manufacturer;

/**
 * ManufacturerSearch represents the model behind the search form about `panix\shop\models\Manufacturer`.
 */
class ManufacturerSearch extends Manufacturer {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Manufacturer::find()->translate();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
           // 'sort' => self::getSort()
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'translate.name', $this->name]);

        return $dataProvider;
    }

}
