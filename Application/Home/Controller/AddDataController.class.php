<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/17
 * Time: 15:34
 */

namespace Home\Controller;
use Think\Controller;

class AddDataController extends Controller
{
    public function index(){
        $this->assign('Alltheme',$this->getAllTheme());
        $user = D('user');
        $this->assign('users', $user->getUserIdAndUserAccount());
        $shop = D('shop');
        $this->assign('shops', $shop->getShopsIdAndshopsName());
        $this->display();
    }

    /*
     * 获取所有主题
     * */
    private function getAllTheme(){
        $allthemeid = D("theme");
        $Res = $allthemeid->getAllTheme();
        return $Res;
    }

    //http://www.thinkphp.cn/code/685.html
    //http://www.thinkphp.cn/topic/15565.html
    /**
     * 上传多张图片
     */
    public function uploadMany() {
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 10485760 ;// 设置附件上传大小10M
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->replace = true;
            $upload->savePath = ''; // 设置附件上传目录
            $upload->saveName = date('Ymd', time()).'_'.mt_rand().'_many';
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
                        $this->error('图片上传失败，数据库连接失败或插入数据非法');
                    }
                    echo $o;
                }
            }
        }
    }

    /**
     * @param $formFileName
     * @return string
     * 上传单个图片
     */
    public function upload($formFileName){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小3M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        //http://www.thinkphp.cn/topic/14727.html
        //提示上传根目录不存在
        $upload->savePath = ''; // 设置附件上传目录
        $upload->saveName = date('Ymd', time()).'_'.mt_rand();
        // 上传单个文件
        $info = $upload->uploadOne($_FILES[$formFileName]);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }

    /**
     * @param $username
     * 异步请求判断账户是否已存在，存在返回true，否则返回false
     */
    public function isUserExists($username){
        $user = D("user");
        if ($user->isUserExists($username)) {
            $data['msg'] = true;
        } else {
            $data['msg'] = false;
        }
        $this->ajaxReturn($data);
    }

    /**
     * 用户注册
     */
    public function user(){
        $user = D("user");
        $postData['username'] = I('post.username');
        $postData['password'] = I('post.password');
        //http://www.thinkphp.cn/topic/30085.html
        //怎样让上传的图片文件和表单其它数据一起提交
        if(!empty($_FILES['user_photo_url'])){
            $savePath = $this->upload('user_photo_url');
            $postData['user_photo_url'] = "/Uploads/".$savePath;
        }

        $postData['signature'] = I('post.signature');
        $postData['nick_name'] = I('post.nick_name');
        $postData['person_group'] = I('post.person_group');
        if(empty($postData['person_group'])){
            $this->error('所属人群不能为空！');
        }
        $postData['phone'] = I('post.phone');
        if(empty($postData['phone'])){
            $this->error('手机号码不能为空！');
        }
        $postData['sex'] = I('post.sex');
        $optionRes = $user->doUserRegister($postData);
        if($optionRes == -1){
            $this->error('用户注册失败，用户名或密码不能为空！');
        } /*elseif($optionRes == -2){
            $this->error('用户注册失败，用户已存在！');
        }*/ elseif($optionRes > 0){
            $this->success('用户注册成功');
        } else {
            $this->error('用户注册失败，数据库连接失败或插入数据非法！');
        }
    }

    /**
     * @param $themeTitle
     * 检查主题是否存在
     */
    public function isThemeExists($themeTitle){
        $theme = D("theme");
        if ($theme->isThemeExists($themeTitle)) {
            $data['msg'] = true;
        } else {
            $data['msg'] = false;
        }
        $this->ajaxReturn($data);
    }

    /**
     * 添加主题
     */
    public function theme(){
        $theme = D("theme");
        $title = I('post.theme_title');
        if(empty($title)){
            $this->error('添加主题失败，主题标题不能为空！');
        }
        $desc = I('post.desc');
        if(empty($desc)){
            $this->error('添加主题失败，主题描述不能为空！');
        }
        $type = I('post.theme_type');
        if(empty($type)){
            $this->error('添加主题失败，主题类型不能为空！');
        }
        if(!empty($_FILES['theme_photo_url'])){
            $savePath = $this->upload('theme_photo_url');
            $photoUrl = "/Uploads/".$savePath;
        }

        $optionRes = $theme->doAddTheme($title, $desc, $type, $photoUrl);
        if($optionRes){
            $this->success('添加主题成功！');
        } else {
            $this->error('添加主题失败，数据库连接失败或插入数据非法！');
        }
    }

    /**
     * 添加特色店
     */
    public function shop(){
        $shop = D('shop');
        $str1 = "-1";
        $cids = I('post.carousel_photo_url_ids');
        foreach($cids as $id){
            $str1 = $str1.';'.$id;
        }
//        dump($str1);exit;

        $str2 = "-1";
        $dids = I('post.desc_photo_url_ids');
        foreach($dids as $id){
            $str2 = $str2.';'.$id;
        }

        if(!empty($_FILES['shop_photo_url'])){
            $savePath = $this->upload('shop_photo_url');
            $photo_url = "/Uploads/".$savePath;
        }
        //判断是否接收到主题，如果是，将主题ID以,分隔后存储
        if(isset($_POST['theme'])){
            $theme=implode(',',I('post.theme'));
        }else{
            $theme='null';
        }

        $result = $shop->doAddShop(I('post.shop_name'),I('post.location'),
            $photo_url,I('post.desc'),$theme,I('post.detail'),$str1,$str2);
        if($result > 0){
            $this->success('添加特色店成功！');
        } else {
            switch($result){
                case -1 :  $this->error('添加失败！店名不得为空！');break;
                case -2 :  $this->error('添加失败！店名长度不合法！');break;
                case -3 :  $this->error('添加失败！店名被占用！');break;
                case -4 :  $this->error('添加失败！店铺网址不能为空！');break;
                case -5 :  $this->error('添加失败！店铺网址不合法！');break;
                default: $this->error('添加失败！未知错误！');
            }
        }
    }

    /**
     * 添加单品
     */
    public function item(){
        $item = D("item");
        $optionRes = $item->doAddItem(I('post.shop_id'),I('post.item_photo_url'),I('post.item_name'),I('post.desc'),I('post.price'));
        if($optionRes){
            $this->success('添加单品成功！');
        } else {
            $this->error('添加单品失败,数据库连接失败或插入数据非法！');
        }
    }

    /**
     * 添加评论
     */
    public function comment(){
        $postData['comment_content'] = I('post.comment_content');
        $postData['comment_user_id'] = I('post.comment_user_id');
        $postData['comment_shop_id'] = I('post.comment_shop_id');
        $strBuilder = "-1";
        $ids = I('post.comment_photo_ids');
        foreach($ids as $id){
            $strBuilder = $strBuilder.';'.$id;
        }
        $postData['comment_photo_ids'] = $strBuilder;
        $comm = D('comment');
        if(!$comm->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            exit($comm->getError());
        } else{
            $optionRes = $comm->doAddComment($postData);
            if($optionRes){
                $this->success('添加评论成功！');
            } else {
                $this->error('添加评论失败,数据库连接失败或插入数据非法！');
            }
        }
    }

}