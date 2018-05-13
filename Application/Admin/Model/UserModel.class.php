<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/1/16
 * Time: 18:39
 */

namespace Admin\Model;
use Think\Model;

class UserModel extends Model{

    public function delUserById($userId){
        return $this->where(array("user_id"=>$userId))->delete();
    }

    public function findUserByUserId($userId){
        return $this->where(array("user_id"=>$userId))->find();
    }

    public function doEditUser($userId, $signature, $nick, $photoUrl, $group, $phone, $sex){
        $currTime=getCurrDateStr();
        $arr=array("signature"=>$signature,
            "nick_name"=>$nick,
            "user_photo_url"=>$photoUrl,
            "person_group"=>$group,
            "phone"=>$phone,
            "sex"=>$sex,
            "addTime"=>$currTime);
        return $this->where(array("user_id"=>$userId))->save($arr);
    }

    public function getUserPhotoUrl($userId){
        return $this->where(array("user_id"=>$userId))->getField('user_photo_url');
    }
}