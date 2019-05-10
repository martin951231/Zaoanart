<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%met}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property string $created_at
 * @property string $updated_at
 */
class Met extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%met}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '材质名称',
            'price' => '材质单价',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}
