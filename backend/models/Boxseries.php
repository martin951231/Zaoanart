<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%boxseries}}".
 *
 * @property int $id
 * @property string $cate_name 颜色系列名
 * @property string $created_at
 * @property string $update_at
 */
class Boxseries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%boxseries}}';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'update_at'], 'safe'],
            [['series_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'series_name' => '色系名',
            'created_at' => '添加时间',
            'update_at' => '修改时间',
        ];
    }
}
