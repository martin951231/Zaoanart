<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Keep;
use backend\models\Account;

/**
 * KeepSearch represents the model behind the search form of `backend\models\Keep`.
 */
class KeepSearch extends Keep
{
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','status','topping'], 'integer'],
            [['created_at', 'updated_at', 'keep_name','uid','username'], 'safe'],
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
        $query = Keep::find();
        $query->joinWith(['account']);
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
            'tsy_keep.id' => $this->id,
//            'uid' => $this->uid,
//            'tsy_account.username' => $this->uid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['like', 'keep_name', $this->keep_name])
            ->andFilterWhere(['like', 'tsy_account.username', $this->uid]);
        return $dataProvider;
    }
}
