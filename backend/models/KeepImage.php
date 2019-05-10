<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%keep_image}}".
 *
 * @property string $id
 * @property int $imgid
 * @property string $created_at
 * @property string $updated_at
 * @property int $kid
 */
class KeepImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%keep_image}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['imgid', 'kid'], 'required'],
            [['imgid', 'kid'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'imgid' => 'Imgid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'kid' => 'Kid',
        ];
    }
}
