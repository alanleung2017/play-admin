<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/12/24
 * Time: 16:11
 */

namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller
{
    //后台登录页面
    public function index(){
        $this->display();
    }

    public function check(){
        // dump($_POST);
        $code=new \Think\Verify();
        if(!$code->check($_POST['fcode'])){
            $this->error('验证码错误！',U('index'));
        }else{
            $_where['username']=$_POST['username'];
            $_where['password']=md5($_POST['password']);
            $res=M('userBackend')->where($_where)->find();
            // dump($res);
            if($res){
//                $_SESSION['admin_username']=$_POST['username'];
                $_SESSION['admin_login']=1;
                $_SESSION['admin_name']=$_POST['username'];
                $this->success('登录成功',U('index/index'));//index控制器的index方法
            }else{
                $this->error('用户名或密码错误！',U('index'));//当前控制器的index方法
            }
        }
    }

    public function verify(){
        $config = array(
            'imageH'    =>    80,    // 验证码高度
            'length'      =>    4,     // 验证码位数
            'codeSet' => '0123456789',
            'fontSize'    =>    40    // 验证码字体大小
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }
}