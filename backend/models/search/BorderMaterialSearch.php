<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BorderMaterial;
use backend\models\Boxseries;

/**
 * BorderMaterialSearch represents the model behind the search form of `backend\models\BorderMaterial`.
 */
class BorderMaterialSearch extends BorderMaterial
{
    public $series_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['img_name','border_name','preview_img'], 'safe'],
            [['price','face_width','Thickness','cate','sid'], 'number'],
            [['series_name'],'safe'],
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
        $query = BorderMaterial::find();
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
            'price' => $this->price,
            'face_width' => $this->face_width,
            'sid' => $this->sid,
            'Thickness' => $this->Thickness,
            'cate' => $this->cate,
            'preview_img' => $this->preview_img,
        ]);
        $query->andFilterWhere(['like', 'img_name', $this->img_name])
              ->andFilterWhere(['like', 'border_name', $this->border_name]);
        return $dataProvider;
    }
}
