<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%decoration_price}}".
 *
 * @property int $id
 * @property string $decoration_method 装裱方式
 * @property int $decoration_code 装裱方式代码(1.满裱  2.卡纸(白卡) 3.卡纸(色卡) 4.盒子(悬浮) 5.盒子(无悬浮) 6.套框绷架 7.单立体 8.纯画芯 9.无框绷架)
 * @property string $price 价格
 * @property string $margin_price 留白价格
 */
class DecorationPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%decoration_price}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['decoration_method', 'decoration_code', 'price', 'margin_price'], 'required'],
            [['decoration_code'], 'integer'],
            [['price', 'margin_price'], 'number'],
            [['decoration_method'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'decoration_method' => '装裱方式',
            'decoration_code' => '装裱方式id',
            'price' => '价格',
            'margin_price' => '留白价格',
        ];
    }
}
