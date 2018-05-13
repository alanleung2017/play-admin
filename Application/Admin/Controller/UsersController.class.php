<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/1/14
 * Time: 12:19
 */

namespace Admin\Controller;

class UsersController extends AuthController{

    public function index(){
        if(empty(I('get.isSearch'))){
            $count = M('user')->count("user_id");
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里
            $list = M('user')->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            for($i=0; $i<count($list); $i++) {
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['user_photo_url']);
                $list[$i]['user_photo_url'] = C('PIC_URL_PREFIX') . $photoUrl;
            }
            $this->assign('userList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->display('userList');
        }else{
            $condition=I('get.searchCondition');
            $_where['user_account']=array('like','%'.$condition.'%');
            $count = M('user')->where($_where)->count("user_id");
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里

            $list = M('user')->where($_where)->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            for($i=0; $i<count($list); $i++) {
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['user_photo_url']);
                $list[$i]['user_photo_url'] = C('PIC_URL_PREFIX') . $photoUrl;
            }
            $this->assign('userList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->assign('searchCondition',$condition);
            $this->display('userList');
        }
    }

    public function delUser($userId){
        $userModel=D('user');
        $photosModel=D('photos');
        $photoId=$userModel->getUserPhotoUrl($userId);
        $userModel->startTrans();
        $option1=$userModel->delUserById($userId);
        if(empty($photoId)){
            $option2=true;
        } else{
            $option2=$photosModel->delPhotoById($photoId);
        }
        /*p('$option1:'.$option1);
        p('$option2:'.$option2);*/
        if($option1 && $option2){
            $userModel->commit();//成功则提交
            $this->success('删除用户成功！');
        }else{
            $userModel->rollback();//不成功，则回滚
            $this->error('删除用户失败，数据库连接错误！');
        }
    }

    public function editUser(){
        if(IS_GET){
            $userId=I("get.userId");
            $userModel=D('user');
            $res=$userModel->findUserByUserId($userId);
//            dump($res);exit;
            $res['o_user_photo_url']=$res['user_photo_url'];
            $photoUrl=D('photos')->getPhotoUrlByID($res['user_photo_url']);
            $res['user_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            $this->assign('userInfo',$res);
            $this->display('newEditUser');
        }else{
            $signature = I('post.signature');
            if(empty($signature)){
                $this->error('修改用户信息失败，签名不能为空！');
            }
            $nick = I('post.nick_name');
            if(empty($nick)){
                $this->error('修改用户信息失败，昵称不能为空！');
            }
            $group = I('post.person_group');
            if(empty($group)){
                $this->error('修改用户信息失败，所属人群不能为空！');
            }
            $phone=I('post.phone');
            if(empty($phone)){
                $this->error('修改用户信息失败，手机号码不能为空！');
            }
            $sex=I('post.sex');
            if(empty($sex)){
                $this->error('修改用户信息失败，性别不能为空！');
            }

            $photoUrl='';
            if(!empty(I('post.user_photo_url'))){
                $photoUrl=I('post.user_photo_url');
            }else{
                $photoUrl=I('post.o_user_photo_url');
            }

            $user = D("user");
            $optionRes = $user->doEditUser(I('post.user_id'),$signature, $nick, $photoUrl, $group, $phone, $sex);
            if($optionRes>0){
                $this->success('修改用户信息成功！');
            } else {
                $this->error('修改用户信息失败，数据库连接失败或插入数据非法！');
            }
        }
    }

    public function uploadUserPic(){
        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    10485760, // 设置附件上传大小10M
                'savePath'   =>    'User/', // 设置上传目录
                'saveName'   =>    '', //保持上传的文件名不变
                'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'), // 设置附件上传类型
                'autoSub'    =>    false, //不使用子目录
                'replace'    =>    true //存在同名是否覆盖
            );
            $upload = new \Think\Upload($config);// 实例化上传类

            $info = $upload->upload();
            if(!$info) {
                //上传错误提示信息
                $this->error($upload->getError());
            } else {
                foreach($info as $file){
                    $data['photo_url'] = '/Uploads/'.$file['savepath'].$file['savename'];
                    $data['addTime'] = getCurrDateStr();
                    $o = M('photos')->add($data);

                    if(false === $o){
                        $arr['status']  = 0;
                        $arr['content'] = '图片上传失败，数据库连接失败或插入数据非法';
                        $this->ajaxReturn($arr);
                    }
                    $arr['status']  = 1;
                    $arr['content'] = C('PIC_URL_PREFIX').$data['photo_url'];
                    $arr['photo_url_id'] = $o;
                    $this->ajaxReturn($arr);
                }
            }
        }
    }

    public function changePwd(){
        if(IS_GET){
            $this->display('newChangePwd');
        }else{
            if(empty(I('post.newPwd'))||empty(I('post.newPwd'))){
                $this->error('两次输入密码都不能为空！');
            }else{
                if(I('post.newPwd')!==I('post.newPwd2')){
                    $this->error('两次输入密码不一致！');
                }else{
                    $data['password']=md5(I('post.newPwd'));
                    $r=M('userBackend')->where(array("username"=>session('admin_name')))->save($data);
                    if($r){
                        $this->success('修改密码成功',U('index/index'));//index控制器的index方法
                    }else{
                        $this->error('修改密码失败，数据库连接失败或插入数据非法！',U('changePwd'));//当前控制器的changePwd方法
                    }
                }
            }
        }
    }
}