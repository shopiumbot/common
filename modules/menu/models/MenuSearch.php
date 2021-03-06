<?php

namespace core\modules\menu\models;

use Yii;
use yii\base\Model;
use panix\engine\data\ActiveDataProvider;

/**
 * MenuSearch represents the model behind the search form about `core\modules\menu\models\Menu`.
 */
class MenuSearch extends Menu
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'string'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $query = Menu::find();
        $query->translate();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => self::getSort(),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->name) {
            $query->andFilterWhere(['like', 'translate.name', $this->name]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        return $dataProvider;
    }

    public static function getSort()
    {
        $sort = new \yii\data\Sort([
            'attributes' => [
                'name' => [
                    'asc' => ['translate.name' => SORT_ASC],
                    'desc' => ['translate.name' => SORT_DESC],
                ],
            ],
        ]);
        return $sort;
    }
}
