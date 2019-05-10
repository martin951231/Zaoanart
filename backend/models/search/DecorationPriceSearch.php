<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DecorationPrice;

/**
 * DecorationPriceSearch represents the model behind the search form of `backend\models\DecorationPrice`.
 */
class DecorationPriceSearch extends DecorationPrice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'decoration_code'], 'integer'],
            [['decoration_method'], 'safe'],
            [['price', 'float_scale'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = DecorationPrice::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'decoration_code' => $this->decoration_code,
            'price' => $this->price,
            'float_scale' => $this->float_scale,
        ]);

        $query->andFilterWhere(['like', 'decoration_method', $this->decoration_method]);

        return $dataProvider;
    }
}
