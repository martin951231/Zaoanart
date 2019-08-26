<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%attention}}".
 *
 * @property int $id
 * @property int $kid 收藏夹id
 * @property int $Attention 关注量
 * @property int $uid 用户
 */
class Attention extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%attention}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kid', 'Attention', 'uid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kid' => '收藏夹id',
            'Attention' => '关注量',
            'uid' => '用户',
        ];
    }
}
