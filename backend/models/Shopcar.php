<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%shopcar}}".
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property int $user_id 用户id
 * @property string $color 卡纸颜色
 * @property double $img_width 图片宽
 * @property double $img_height 图片高
 * @property double $box_width 框宽
 * @property double $box_height 框高
 * @property string $decoration_status 装裱方式
 * @property string $core_material 画芯材质
 * @property double $drawing_core_val 画芯留边值
 * @property double $core_offset 画芯偏移值
 * @property string $core_offset_direction 画芯偏移方向
 * @property double $core_shift_val 留边偏移值
 * @property string $core_shift_direction 留边偏移方向
 * @property int $core_price 画芯价格
 * @property int $decoration_price 装裱价格
 * @property int $total_price 总价
 * @property string $created_at 添加时间
 */
class Shopcar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shopcar}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'user_id', 'color', 'img_width', 'img_height', 'box_width', 'box_height', 'decoration_status', 'core_material', 'drawing_core_val', 'core_offset', 'core_offset_direction', 'core_shift_val', 'core_shift_direction', 'core_price', 'decoration_price', 'total_price'], 'required'],
            [['goods_id', 'user_id', 'core_price', 'decoration_price', 'total_price'], 'integer'],
            [['img_width', 'img_height', 'box_width', 'box_height', 'drawing_core_val', 'core_offset', 'core_shift_val'], 'number'],
            [['created_at'], 'safe'],
            [['color', 'decoration_status', 'core_material','box_name', 'core_offset_direction', 'core_shift_direction','excel_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'user_id' => '用户id',
            'color' => '卡纸颜色',
            'box_name' => '框名',
            'img_name' => '图片',
            'img_width' => '图片宽',
            'img_height' => '图片高',
            'box_width' => '框宽',
            'box_height' => '框高',
            'decoration_status' => '装裱方式',
            'core_material' => '画芯材质',
            'drawing_core_val' => '画芯留边值',
            'core_offset' => '画芯偏移值',
            'core_offset_direction' => '画芯偏移方向',
            'core_shift_val' => '留边偏移值',
            'core_shift_direction' => '留边偏移方向',
            'core_price' => '画芯价格',
            'decoration_price' => '装裱价格',
            'total_price' => '总价',
            'excel_name'=> 'excel',
            'created_at' => '添加时间',
        ];
    }
}
