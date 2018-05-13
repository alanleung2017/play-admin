<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/18
 * Time: 16:53
 */

namespace Home\Model;
use Think\Model;

class PhotosModel extends Model
{
    public function getPhotoUrlByID($photoID){
        return $this->where(array('photo_id'=>$photoID))->getField('photo_url');
    }
}