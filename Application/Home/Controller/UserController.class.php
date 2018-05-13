<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 16:32
 */

namespace Home\Controller;
use Think\Controller;
use Common\Common\Utils\Response;

class UserController extends Controller
{
    public function register(){
        $user_number=I('post.user_number');
        $password=I('post.password');
        $user_sex=I('post.user_sex');
        $person_group=I('post.person_group');
        if(empty($user_number) || empty($password) ||
            empty($user_sex) || empty($person_group)){
            return Response::show(400, '注册失败，存在空的参数', array());
        } else {
            $r=D('user')->doRegister($user_number, $password, $user_sex, $person_group);
            if($r !== false){
                $data = D('user')->findUserByPhone($user_number);
                return Response::show(200, '注册成功', $data);
            } else {
                return Response::show(400, '注册失败，数据库连接错误', array());
            }
        }
    }

    public function login(){
        $user_number = I('post.user_number');
        $password = I('post.password');
        if(empty($user_number) || empty($password)){
            return Response::show(400, '登录失败，手机号码或密码不能为空', array());
        } else{
            $r=D('user')->doLogin($user_number, $password);
            if($r !== false && $r !== -1){
                $data = D('user')->findUserByPhone($user_number);
                $url=D('photos')->getPhotoUrlByID($data['user_photo_url']);
                $data['user_photo_url'] = C('PIC_URL_PREFIX').$url;
                return Response::show(200, '登录成功', $data);
            } elseif($r === -1){
                return Response::show(404, '登录失败，手机号码或密码错误', array());
            } else{
                return Response::show(400, '登录失败，数据库连接错误', array());
            }
        }
    }
}