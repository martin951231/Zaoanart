<?php

namespace backend\models;
use backend\models\Boxseries;

use Yii;

/**
 * This is the model class for table "{{%border_material}}".
 *
 * @property int $id
 * @property string $img_name
 * @property string $price 价格
 * @property string $border_name 边框名
 * @property int $face_width 面宽
 * @property int $Thickness 材厚
 * @property string $created_at
 * @property string $updated_at
 */
class BorderMaterial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%border_material}}';
    }
//    public function getSid()
//    {
//        return $this->hasOne(Boxseries::className(), ['id' => 'sid']);
//    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['img_name'], 'required'],
            [['price'], 'number'],
            [['face_width', 'Thickness','cate','sid'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['img_name', 'border_name','preview_img'], 'string', 'max' => 255],
            [['img_name'], 'file', 'maxFiles' => 500,'extensions'=>'jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img_name' => '图片',
            'price' => '价格',
            'border_name' => '边框名',
            'face_width' => '面宽',
            'preview_img' => '缩略图名',
            'Thickness' => '侧厚',
            'sid'=>'色系',
            'cate' => '分类',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
