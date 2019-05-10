<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Shopcar;

/**
 * ShopcarSearch represents the model behind the search form of `backend\models\Shopcar`.
 */
class ShopcarSearch extends Shopcar
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'goods_id', 'user_id', 'core_price', 'decoration_price', 'total_price', 'status'], 'integer'],
            [['color', 'img_name', 'decoration_status', 'core_material', 'core_offset_direction', 'core_shift_direction', 'created_at', 'box_name', 'excel_name'], 'safe'],
            [['img_width', 'img_height', 'box_width', 'box_height', 'drawing_core_val', 'core_offset', 'core_shift_val'], 'number'],
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
        $query = Shopcar::find();

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
            'goods_id' => $this->goods_id,
            'user_id' => $this->user_id,
            'img_width' => $this->img_width,
            'img_height' => $this->img_height,
            'box_width' => $this->box_width,
            'box_height' => $this->box_height,
            'drawing_core_val' => $this->drawing_core_val,
            'core_offset' => $this->core_offset,
            'core_shift_val' => $this->core_shift_val,
            'core_price' => $this->core_price,
            'decoration_price' => $this->decoration_price,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'color', $this->color])
            ->andFilterWhere(['like', 'img_name', $this->img_name])
            ->andFilterWhere(['like', 'decoration_status', $this->decoration_status])
            ->andFilterWhere(['like', 'core_material', $this->core_material])
            ->andFilterWhere(['like', 'core_offset_direction', $this->core_offset_direction])
            ->andFilterWhere(['like', 'core_shift_direction', $this->core_shift_direction])
            ->andFilterWhere(['like', 'box_name', $this->box_name])
            ->andFilterWhere(['like', 'excel_name', $this->excel_name]);

        return $dataProvider;
    }
}
