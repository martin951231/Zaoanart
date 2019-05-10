<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%history}}".
 *
 * @property int $id
 * @property string $created_at
 * @property string $creates_at
 * @property int $imgid
 * @property int $uid
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'creates_at'], 'safe'],
            [['imgid', 'uid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'creates_at' => 'Creates At',
            'imgid' => 'Imgid',
            'uid' => 'Uid',
        ];
    }
}
