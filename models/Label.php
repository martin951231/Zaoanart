<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%label}}".
 *
 * @property integer $id
 * @property string $label_name
 * @property string $created_at
 * @property string $updated_at
 */
class Label extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%label}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['label_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label_name' => '标签名',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
