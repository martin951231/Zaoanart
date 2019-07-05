<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property int $id
 * @property int $uid
 * @property string $content
 * @property string $created_at
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid'], 'required'],
            [['uid'], 'integer'],
            [['created_at'], 'safe'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'content' => '留言内容',
            'created_at' => '留言时间',
        ];
    }
}
