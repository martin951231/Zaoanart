<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%pv}}".
 *
 * @property int $id
 * @property string $page_name
 * @property int $count
 */
class Pv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%pv}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count'], 'integer'],
            [['page_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_name' => 'Page Name',
            'count' => 'Count',
        ];
    }
}
