<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%account}}".
 *
 * @property integer $id
 * @property integer $account_role
 * @property string $username
 * @property string $password
 * @property string $nickname
 * @property string $avatar
 * @property string $ip_address
 * @property string $phone
 * @property string $weixin
 * @property string $gender
 * @property string $position
 * @property string $birthday
 * @property integer $is_deleted
 * @property string $last_login_time
 * @property string $created_at
 * @property string $updated_at
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_role', 'is_deleted'], 'integer'],
            [['username'], 'required'],
            [['birthday', 'last_login_time', 'created_at', 'updated_at'], 'safe'],
            [['username', 'password', 'phone'], 'string', 'max' => 32],
            [['nickname'], 'string', 'max' => 64],
            [['avatar', 'ip_address', 'position'], 'string', 'max' => 255],
            [['weixin'], 'string', 'max' => 50],
            [['gender'], 'string', 'max' => 2],
            [['avatar'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'account_role' => 'Account Role',
            'username' => '用户名',
            'password' => '密码',
            'nickname' => '昵称',
            'avatar' => '头像',
            'ip_address' => 'Ip地址',
            'phone' => '电话',
            'weixin' => '微信',
            'gender' => '性别',
            'position' => '定位',
            'birthday' => '生日',
            'is_deleted' => '状态',
            'last_login_time' => '上次登录时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间
            ',
        ];
    }

    public function upload()
    {
        if($this->validate()){
            $this->avatar->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        }else{
            return false;
        }
    }
}
