<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%filterimg}}".
 *
 * @property int $id
 * @property int $uid
 * @property int $imgid
 * @property string $filter_img 带滤镜的图片名
 * @property string $created_at
 * @property string $updated_at
 * @property int $img_width
 * @property int $img_height
 */
class Filterimg extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%filterimg}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'imgid'], 'required'],
            [['uid', 'imgid', 'img_width', 'img_height'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['filter_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'imgid' => 'Imgid',
            'filter_img' => '带滤镜的图片名',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'img_width' => 'Img Width',
            'img_height' => 'Img Height',
        ];
    }
}
