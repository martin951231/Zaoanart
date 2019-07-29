<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%login}}".
 *
 * @property int $id
 * @property int $login_sum
 * @property string $created_at
 * @property string $login_ip
 * @property string $login_city
 */
class Login extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%login}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login_sum'], 'integer'],
            [['created_at'], 'safe'],
            [['login_ip', 'login_city'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login_sum' => 'Login Sum',
            'created_at' => 'Created At',
            'login_ip' => 'Login Ip',
            'login_city' => 'Login City',
        ];
    }
}
