<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Goods;
use backend\models\Category;
use backend\models\Theme;
use backend\models\Page;

/**
 * GoodsSearch represents the model behind the search form about `backend\models\Goods`.
 */
class GoodsSearch extends Goods
{
    public $category_name;
    public $theme_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category', 'theme', 'shape', 'color', 'is_appear', 'is_recommend','is_face','is_login'], 'integer'],
            [['name', 'label','time', 'image', 'author', 'type', 'length', 'width', 'title', 'content', 'link', 'introduction', 'review', 'created_at', 'updated_at'], 'safe'],
            [['max_length', 'max_width', 'min_length', 'min_width', 'price', 'premium'], 'number'],
            [['category_name'],'safe'],
            [['theme_name'],'safe'],
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
        $query = Goods::find();
        $query->joinWith(['category']);
        $query->joinWith(['theme']);
        $uid = Yii::$app->user->identity->id;
        // add conditions that should always apply here
        $num = Page::find()->select('pagesize')->where(['uid'=>$uid])->one();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => $num['pagesize'],
         ]
        ]);
        $this->load($params);

        $dataProvider->setSort([   //添加-----排序
            'attributes' => [
                'id' => [
                    'asc' => [Goods::tableName().'.id' => SORT_ASC],
                    'desc' => [Goods::tableName().'.id' => SORT_DESC],
                    'label' => 'id'
                ],
                'name' => [
                    'asc' => [Goods::tableName().'.name' => SORT_ASC],
                    'desc' => [Goods::tableName().'.name' => SORT_DESC],
                    'label' => 'name'
                ],
                'image' => [
                    'asc' => [Goods::tableName().'.image' => SORT_ASC],
                    'desc' => [Goods::tableName().'.image' => SORT_DESC],
                    'label' => 'image'
                ],
                'author' => [
                    'asc' => [Goods::tableName().'.author' => SORT_ASC],
                    'desc' => [Goods::tableName().'.author' => SORT_DESC],
                    'label' => 'author'
                ],
                'category_name' => [
                    'asc' => ['tsy_category.category_name' => SORT_ASC],
                    'desc' => ['tsy_category.category_name' => SORT_DESC],
                    'label' => '商品分类'
                ],
                'theme_name' => [
                    'asc' => ['tsy_theme.theme_name' => SORT_ASC],
                    'desc' => ['tsy_theme.theme_name' => SORT_DESC],
                    'label' => '商品主题'
                ],
                'time' => [
                    'asc' => [Goods::tableName().'.time' => SORT_ASC],
                    'desc' => [Goods::tableName().'.time' => SORT_DESC],
                    'label' => 'time'
                ],
                'max_length' => [
                    'asc' => [Goods::tableName().'.max_length' => SORT_ASC],
                    'desc' => [Goods::tableName().'.max_length' => SORT_DESC],
                    'label' => 'max_length'
                ],
                'max_width' => [
                    'asc' => [Goods::tableName().'.max_width' => SORT_ASC],
                    'desc' => [Goods::tableName().'.max_width' => SORT_DESC],
                    'label' => 'max_width'
                ],
                'premium' => [
                    'asc' => [Goods::tableName().'.premium' => SORT_ASC],
                    'desc' => [Goods::tableName().'.premium' => SORT_DESC],
                    'label' => 'premium'
                ],
                'content' => [
                    'asc' => [Goods::tableName().'.content' => SORT_ASC],
                    'desc' => [Goods::tableName().'.content' => SORT_DESC],
                    'label' => 'content'
                ],
                'is_appear' => [
                    'asc' => [Goods::tableName().'.is_appear' => SORT_ASC],
                    'desc' => [Goods::tableName().'.is_appear' => SORT_DESC],
                    'label' => 'is_appear'
                ],
                'is_face' => [
                    'asc' => [Goods::tableName().'.is_face' => SORT_ASC],
                    'desc' => [Goods::tableName().'.is_face' => SORT_DESC],
                    'label' => 'is_face'
                ],
                'is_recommend' => [
                    'asc' => [Goods::tableName().'.is_recommend' => SORT_ASC],
                    'desc' => [Goods::tableName().'.is_recommend' => SORT_DESC],
                    'label' => 'is_recommend'
                ],
                'color' => [
                    'asc' => [Goods::tableName().'.color' => SORT_ASC],
                    'desc' => [Goods::tableName().'.color' => SORT_DESC],
                    'label' => 'color'
                ],
                'label' => [
                    'asc' => [Goods::tableName().'.label' => SORT_ASC],
                    'desc' => [Goods::tableName().'.label' => SORT_DESC],
                    'label' => 'label'
                ],
                'is_login' => [
                    'asc' => [Goods::tableName().'.is_login' => SORT_ASC],
                    'desc' => [Goods::tableName().'.is_login' => SORT_DESC],
                    'label' => 'is_login'
                ],
            ]
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        var_dump($this->id);die;
        // grid filtering conditions
        $query->andFilterWhere([
            'tsy_goods.id' => $this->id,
            'category' => $this->category,
            'theme' => $this->theme,
            'max_length' => $this->max_length,
            'max_width' => $this->max_width,
            'min_length' => $this->min_length,
            'min_width' => $this->min_width,
            'shape' => $this->shape,
            'price' => $this->price,
            'premium' => $this->premium,
            'color' => $this->color,
            'is_appear' => $this->is_appear,
            'is_face' => $this->is_face,
            'is_login' => $this->is_login,
            'is_recommend' => $this->is_recommend,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'time', $this->time])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'length', $this->length])
            ->andFilterWhere(['like', 'width', $this->width])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'introduction', $this->introduction])
            ->andFilterWhere(['like', 'review', $this->review])
            ->andFilterWhere(['like', 'tsy_category.category_name', $this->category_name])
            ->andFilterWhere(['like', 'tsy_theme.theme_name', $this->theme_name]);
        return $dataProvider;
    }
}
