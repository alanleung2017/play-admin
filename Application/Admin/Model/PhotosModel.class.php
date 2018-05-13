<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/18
 * Time: 16:53
 */

namespace Admin\Model;
use Think\Model;

class PhotosModel extends Model
{
    public function getPhotoUrlByID($photoID){
        return $this->where(array('photo_id'=>$photoID))->getField('photo_url');
    }

    public function delPhotoInfoByUrl($photoUrl){
//        dump($photoUrl);
//        dump($this->where(array('photo_url'=>'/Uploads/Theme/20140618023627795.jpg'))->find());
        return $this->where(array('photo_url'=>$photoUrl))->delete();
    }

    public function delPhotoById($photoId){
        return $this->where(array('photo_id'=>$photoId))->delete();
    }
}