<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/12/24
 * Time: 16:16
 */

namespace Admin\Controller;

class LogoutController extends AuthController
{
    //退出登录
    public function index(){
        session(null); // 清空当前的session
        $this->redirect('Admin/Login/index');
    }
}