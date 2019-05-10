<?php

namespace common\models;// 这里的命名空间根据自己的配置，自己来修改

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "information".
 *
 * @property integer $id
 * @property string $name
 * @property string $icon
 * @property string $bigImage
 * @property integer $mbuyId
 * @property string $smallTitle
 * @property string $content
 * @property integer $browseNum
 * @property integer $praiseNum
 * @property integer $commentNum
 * @property integer $status
 * @property integer $recommend
 * @property integer $recitems
 * @property string $createtime
 * @property string $remarks
 * @property string $username
 */
class SmallImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    private $src;
    private $imageinfo;
    private $image;
    public  $percent = 0.1;
    public function __construct($src){

        $this->src = $src;

    }
    public static function tableName()
    {
        return  '{{%goods}}';
    }

    /**
     * @inheritdoc
     */

    /**
     * @inheritdoc
     */

    public function openImage(){
        $smallpath = Yii::getAlias('@backend').'\web\Goods'.'\\'.$this->src;
        list($width, $height, $type, $attr) = getimagesize($smallpath);
        $this->imageinfo = array(

            'width'=>$width,
            'height'=>$height,
            'type'=>image_type_to_extension($type,false),
            'attr'=>$attr
        );
        $fun = 'imagecreatefrom'.$this->imageinfo['type'];
        $this->image = $fun($smallpath);
        return $this->image;
    }
    /**
    操作图片
     */
    public function thumpImage(){

        $new_width = $this->imageinfo['width'] * $this->percent;
        $new_height = $this->imageinfo['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width,$new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump,$this->image,0,0,0,0,$new_width,$new_height,$this->imageinfo['width'],$this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
        return $this->image;
    }
    /**
    输出图片
     */
    public function showImage(){

        header('Content-Type: image/'.$this->imageinfo['type']);
        $funcs = "image".$this->imageinfo['type'];
        var_dump($this->image);
        $funcs($this->image);
    }
    /**
    保存图片到硬盘
     */
    public function saveImage($sname){

        $funcs = "image".$this->imageinfo['type'];
        $funcs($this->image,$sname.$this->imageinfo['type']);

    }
    /**
    销毁图片
     */
    public function __destruct(){

//        imagedestroy($this->image);
    }
}