<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%goods}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $time
 * @property integer $category
 * @property integer $theme
 * @property string $image
 * @property string $author
 * @property integer $type
 * @property string $length
 * @property string $width
 * @property string $max_length
 * @property string $max_width
 * @property string $min_length
 * @property string $min_width
 * @property integer $shape
 * @property string $title
 * @property string $price
 * @property string $premium
 * @property string $content
 * @property integer $color
 * @property string $link
 * @property string $introduction
 * @property string $review
 * @property integer $is_appear
 * @property integer $is_face
 * @property integer $is_recommend
 * @property string $created_at
 * @property string $updated_at
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods}}';
    }

    /**
     * @inheritdoc
     */


    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }
    public function getTheme()
    {
        return $this->hasOne(Theme::className(), ['id' => 'theme']);
    }
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category', 'theme', 'shape', 'color', 'is_appear', 'is_recommend','is_face','is_login'], 'integer'],
            [['category_name'],'safe'],
            [['theme_name'],'safe'],
            [['max_length', 'max_width', 'min_length', 'min_width', 'price', 'premium'], 'number'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['time', 'type', 'review'], 'string', 'max' => 4],
            [['image', 'author', 'title', 'link','label', 'introduction'], 'string', 'max' => 255],
            [['length', 'width'], 'string', 'max' => 10],
            [['image'], 'file', 'maxFiles' => 500,'extensions'=>'jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品ID',
            'name' => '商品名称',
            'time' => '创作年份',
            'category' => '商品分类',
            'theme' => '商品主题',
            'image' => '商品图片',
            'author' => '作者',
            'type' => 'Type',
            'length' => '高度',
            'width' => '宽度',
            'max_length' => '最大高度',
            'max_width' => '最大宽度',
            'min_length' => '最小高度',
            'min_width' => '最小宽度',
            'shape' => 'Shape',
            'title' => 'Title',
            'price' => 'Price',
            'premium' => '溢价指数',
            'content' => '描述',
            'color' => '颜色',
            'label' => '标签',
            'link' => 'Link',
            'introduction' => 'Introduction',
            'review' => '评论',
            'is_appear' => '状态',
            'is_face' => '是否显示图片名',
            'is_recommend' => '是否推荐',
            'is_login' => '是否为登录图片',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}
