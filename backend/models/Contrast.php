<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%contrast}}".
 *
 * @property int $id
 * @property int $contrast_code
 * @property string $contrast_name
 * @property int $search_sum
 * @property int $search_sum_wechat
 */
class Contrast extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%contrast}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contrast_code', 'search_sum', 'search_sum_wechat'], 'integer'],
            [['contrast_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contrast_code' => 'Contrast Code',
            'contrast_name' => 'Contrast Name',
            'search_sum' => 'Search Sum',
            'search_sum_wechat' => 'Search Sum Wechat',
        ];
    }
}
