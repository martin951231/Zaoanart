<?php

namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%label}}".
 *
 * @property integer $id
 * @property string $label_name
 * @property string $created_at
 * @property string $updated_at
 */
class Goods extends ActiveRecord implements IdentityInterface
{
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

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
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category', 'theme', 'shape', 'color', 'is_appear', 'is_recommend'], 'integer'],
            [['category_name'],'safe'],
            [['theme_name'],'safe'],
            [['max_length', 'max_width', 'min_length', 'min_width', 'price', 'premium'], 'number'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['time', 'type', 'review'], 'string', 'max' => 4],
            [['image', 'author', 'title', 'link','label', 'introduction'], 'string', 'max' => 255],
            [['length', 'width'], 'string', 'max' => 10],
            [['image'], 'file', 'maxFiles' => 50,'extensions'=>'jpg'],
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
            'is_recommend' => '是否推荐',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}
