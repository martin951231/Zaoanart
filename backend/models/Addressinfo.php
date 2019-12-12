<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%addressinfo}}".
 *
 * @property int $id
 * @property string $username 姓名
 * @property int $tel 电话
 * @property string $area 地区
 * @property string $address 详细地址
 */
class Addressinfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addressinfo}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'tel'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['area', 'address'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '姓名',
            'tel' => '电话',
            'area' => '地区',
            'address' => '详细地址',
        ];
    }
}
