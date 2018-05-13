<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/12/24
 * Time: 16:07
 */

namespace Admin\Controller;
use Think\Controller;

class AuthController extends Controller
{
    public function _initialize(){
        $admin_login = session('admin_login');
        if(!$admin_login){
            $this->redirect('Admin/Login/index');
        }
    }
}