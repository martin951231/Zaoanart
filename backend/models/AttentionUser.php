<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%attention_user}}".
 *
 * @property int $id
 * @property int $uid 收藏夹id
 * @property int $attention_uid 用户
 * @property string $created_at
 */
class AttentionUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attention_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'attention_uid'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '收藏夹id',
            'attention_uid' => '用户',
            'created_at' => 'Created At',
        ];
    }
}
