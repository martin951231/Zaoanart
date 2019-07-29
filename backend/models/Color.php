<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%color}}".
 *
 * @property int $id
 * @property int $color_code
 * @property string $color_name
 * @property int $search_sum
 * @property int $search_sum_wechat
 */
class Color extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%color}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color_code', 'search_sum', 'search_sum_wechat'], 'integer'],
            [['color_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color_code' => 'Color Code',
            'color_name' => 'Color Name',
            'search_sum' => 'Search Sum',
            'search_sum_wechat' => 'Search Sum Wechat',
        ];
    }
}
