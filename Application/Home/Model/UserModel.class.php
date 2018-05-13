<?php

/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/17
 * Time: 16:00
 */
namespace Home\Model;

use Think\Model;

class UserModel extends Model
{
    /**
     * 判断指定用户名的用户是否存在
     * @param  string $userName 待判断的用户名
     * @return boolean 若用户名存在，返回true；否则返回false
     */
    public function isUserExists($userName)
    {
        // 1. 判断用户名是否有效
        /*if (empty($userName)) {
            return false;
        }*/

        // 2. 查询指定条件的记录个数
        $count = $this->where(array(
            'user_account' => $userName
        ))->count();

        // 返回
        return 1 == $count;
    }

    /**
     * @param array $postData
     * @return bool|mixed
     * 用户注册/新增一个用户
     */
//    public function doUserRegister($postData = array())
//    {
//        // 1. 数据校验
//        if (empty($postData['username']) || empty($postData['password'])) {
//            return -1;
//        }
//        // 2. 判断用户是否存在（若用户已经存在，不能再注册该用户名）
//        //改为前端异步请求
//        /*if ($this->isUserExists($postData['username'])) {
//            return -2;
//        }*/
//
//        // 3. 实现注册操作
//        $data['user_account'] = $postData['username'];
//        $data['password'] = md5($postData['password']);
//        $data['user_photo_url'] = $postData['user_photo_url'];
//        $data['signature'] = $postData['signature'];
//        $data['nick_name'] = $postData['nick_name'];
//        $data['person_group'] = $postData['person_group'];
//        $data['phone'] = $postData['phone'];
//        $data['sex'] = $postData['sex'];
//        $data['addTime'] = getCurrDateStr();
//        //如果写入数据非法则结果$result返回false,如果是自增主键$result则返回主键值，否则返回1。
//        return $this->add($data);
//    }

    /**
     * @param $username
     * @return mixed
     * 根据用户名查找一条用户记录
     */
    public function findUserByUserName($username){
        return $this->where(array("user_name"=>$username))->find();
    }

    /**
     * @return mixed
     * 获取所有的用户记录，每条记录只有user_id和user_account两个字段
     */
    public function getUserIdAndUserAccount(){
        return $this->field('user_id,user_account')->select();
    }

    public function doRegister($phone, $password, $sex, $person_group){
        $data = array(
            'phone'=>$phone,
            'password'=>$password,
            'sex'=>$sex,
            'person_group'=>$person_group,
            'addTime'=>getCurrDateStr()
        );

        return $this->add($data);
    }

    public function findUserByPhone($phone){
        return $this->where(array('phone'=>$phone))->find();
    }

    public function doLogin($phone, $password){
        $map=array(
            'phone'=>$phone,
            'password'=>$password
        );
        $r = $this->where($map)->count('user_id');
        if($r > 0){
            return true;
        } elseif($r == 0){
            return -1;
        } else{
            return false;
        }
    }

    public function findUserByUserId($uid){
        return $this->where(array("user_id"=>$uid))->find();
    }
}