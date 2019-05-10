<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%theme}}".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $category_name
 * @property integer $order_id
 * @property string $created_at
 * @property string $updated_at
 */
class Theme extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%theme}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'order_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['theme_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'theme_name' => '主题名称',
            'order_id' => 'Order ID',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}
