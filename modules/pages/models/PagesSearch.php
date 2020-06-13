<?php

namespace core\modules\pages\models;

use Yii;
use yii\base\Model;
use panix\engine\data\ActiveDataProvider;
use core\modules\pages\models\Pages;
use core\modules\pages\models\PagesTranslate;
/**
 * PagesSearch represents the model behind the search form about `core\modules\pages\models\Pages`.
 */
class PagesSearch extends Pages {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id','views'], 'integer'],
            [['name','created_at'], 'safe'],
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
        $query = Pages::find();

        $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'sort'=> self::getSort(),
                ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'DATE(created_at)', $this->created_at]);
        $query->andFilterWhere(['like', 'DATE(created_at)', $this->created_at]);
        $query->andFilterWhere(['like', 'views', $this->views]);

        return $dataProvider;
    }
    public static function getSort() {
        $sort = new \yii\data\Sort([
            'attributes' => [
                'created_at',
                'updated_at',
                'views',
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                ],
            ],
        ]);
        return $sort;
    }
}
