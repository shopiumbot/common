<?php

namespace core\modules\shop\models\search;

use Yii;
use yii\base\Model;
use panix\engine\data\ActiveDataProvider;
use core\modules\shop\models\Product;

/**
 * ProductSearch represents the model behind the search form about `core\modules\shop\models\Product`.
 */
class ProductSearch extends Product
{

    public $exclude = null;
    public $price_min;
    public $price_max;
    // public $image;
    //public $commentsCount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_min', 'price_max', 'manufacturer_id', 'main_category_id'], 'integer'],
            // [['image'],'boolean'],
            [['sku', 'price', 'id'], 'safe'], //commentsCount
            [['name'], 'string'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d']
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
     * @return ActiveDataProvider
     */
    public function search($params, $configure = [])
    {
        $query = Product::find();
        $query->sort();

        //$query->joinWith(['categorization categories']); //, 'commentsCount'
        $className = substr(strrchr(__CLASS__, "\\"), 1);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => self::getSort(),
            /*'sort22' => [
                //'defaultOrder' => ['created_at' => SORT_ASC],
                'attributes' => [
                    'price',
                    'created_at',
                    'name' => [
                        'asc' => ['name' => SORT_ASC],
                        'desc' => ['name' => SORT_DESC],
                    ]
                ],
            ],*/
        ]);
        if (isset($params[$className]['price']['min'])) {
            $this->price_min = $params[$className]['price']['min'];
            if (!is_numeric($this->price_min)) {
                $this->addError('price', Yii::t('yii', '{attribute} must be a number.', ['attribute' => 'min']));
                return $dataProvider;
            }
        }
        if (isset($params[$className]['price']['max'])) {
            $this->price_max = $params[$className]['price']['max'];
            if (!is_numeric($this->price_max)) {
                $this->addError('price', Yii::t('yii', '{attribute} must be a number.', ['attribute' => 'max']));
                return $dataProvider;
            }
        }


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if (isset($params[$className]['eav'])) {
            $result = array();
            foreach ($params[$className]['eav'] as $name => $eav) {
                if (!empty($eav)) {
                    $result[$name][] = $eav;
                }
            }

            $query->getFindByEavAttributes2($result);
        }

        if (isset($params[$className]['price']['max'])) {
            $query->applyPrice($params[$className]['price']['max'], '<=');
        }
        if (isset($params[$className]['price']['min'])) {
            $query->applyPrice($params[$className]['price']['min'], '>=');
        }

        // Id of product to exclude from search
        if ($this->exclude) {
            foreach ($this->exclude as $id) {
                $query->andFilterWhere(['!=', self::tableName() . '.id', $id]);
            }

        }
        if (strpos($this->id, ',')) {
            $query->andFilterWhere(['in',
                self::tableName() . '.id', explode(',', $this->id),
            ]);
        } else {
            $query->andFilterWhere([
                self::tableName() . '.id' => $this->id,
            ]);
            $query->andFilterWhere(['like', 'name', $this->name]);
        }

        /*$query->andFilterWhere([
            '>=',
            'date_update',
            $this->date_update
        ]);*/


        // $query->andFilterWhere(['between', 'date_update', $this->start, $this->end]);
        //$query->andFilterWhere(['like', "DATE(CONVERT_TZ('date_update', 'UTC', '".Yii::$app->timezone."'))", $this->date_update.' 23:59:59']);
        //  $query->andFilterWhere(['like', "DATE(CONVERT_TZ('date_create', 'UTC', '".Yii::$app->timezone."'))", $this->date_create.]);


        $query->andFilterWhere(['like', 'sku', $this->sku]);
        $query->andFilterWhere(['manufacturer_id' => $this->manufacturer_id]);
        if ($this->main_category_id)
            $query->andFilterWhere(['categories.category'=>$this->main_category_id]);


        //$query->andFilterWhere(['like', 'commentsCount', $this->commentsCount]);
        // echo $query->createCommand()->rawSql; die;
        return $dataProvider;
    }


    public function searchBySite($params)
    {
        $query = Product::find();
        $this->load($params);
        return $query;
    }

}
