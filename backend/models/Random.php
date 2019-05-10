<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%random_tel}}".
 *
 * @property int $id
 * @property string $tel
 * @property string $random
 * @property string $created_at
 */
class Random extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%random_tel}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tel', 'random'], 'required'],
            [['created_at'], 'safe'],
            [['tel'], 'string', 'max' => 32],
            [['random'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tel' => 'Tel',
            'random' => 'Random',
            'created_at' => 'Created At',
        ];
    }
}
