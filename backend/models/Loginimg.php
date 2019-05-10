<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%loginimg}}".
 *
 * @property int $id
 * @property string $img_name 图片名
 * @property string $new_name 保存的名字
 * @property string $created_at
 * @property string $updated_at
 */
class Loginimg extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%loginimg}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'img_name', 'login_img'], 'required'],
            [['id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['img_name', 'login_img'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['img_name','login_img'], 'file', 'maxFiles' => 500,'extensions'=>'jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img_name' => '图片名',
            'login_img' => '新图片名',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
