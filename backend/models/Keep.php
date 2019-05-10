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
 * @property string $keep_name
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

    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'uid']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'keep_name'], 'required'],
            [['uid','status','topping'], 'integer'],
            [['created_at', 'updated_at','username'], 'safe'],
            [['keep_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户名',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
            'keep_name' => '收藏夹名',
            'status'=>'是否推荐',
            'topping'=>'是否置顶'
        ];
    }
}
