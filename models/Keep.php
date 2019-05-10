<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%keep}}".
 *
 * @property string $id
 * @property int $uid
 * @property string $created_at
 * @property string $updated_at
 */
class Keep extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%keep}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'created_at', 'updated_at'], 'required'],
            [['uid'], 'integer'],
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
            'uid' => 'Uid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
