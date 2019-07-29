<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%account_channel}}".
 *
 * @property int $id
 * @property int $channel_pc
 * @property int $channel_wechat
 */
class AccountChannel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%account_channel}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel_pc', 'channel_wechat'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel_pc' => 'Channel Pc',
            'channel_wechat' => 'Channel Wechat',
        ];
    }
}
