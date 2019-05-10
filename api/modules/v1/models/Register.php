<?php

namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%label}}".
 *
 * @property integer $id
 * @property string $label_name
 * @property string $created_at
 * @property string $updated_at
 */
class Register extends ActiveRecord implements IdentityInterface
{
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

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
}
