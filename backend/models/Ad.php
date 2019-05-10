<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * @property integer $id
 * @property string $image
 * @property string $title
 * @property integer $goods_id
 * @property string $link
 * @property boolean $is_appear
 * @property string $created_at
 * @property string $updated_at
 */
class Ad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ad}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string'],
            [['goods_id'], 'integer'],
            [['is_appear'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['image'],'file', 'maxFiles' => 10, 'skipOnEmpty' => false,'extensions'=>'jpg,png,gif'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '广告牌ID',
            'image' => '广告图片',
            'title' => '广告标题',
            'goods_id' => '商品ID',
            'link' => 'Link',
            'is_appear' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function upload()
    {
        if($this->validate()){
            $this->file->saveAs('uploads/' . $this->file->baseName . '.' . $this->file->extension);
            return true;

        }else{
            return false;
        }
    }
}
