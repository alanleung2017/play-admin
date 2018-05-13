<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/13
 * Time: 15:47
 */

namespace Admin\Controller;
use Think\Controller;

class TestController extends Controller
{
    /*public function adminRegist(){
        $admin=M('userBackend');
        $password='play123';
        $passwordEncode=md5($password);
        $arr=array('username'=>'admin','password'=>$passwordEncode);
        $admin->add($arr);
    }*/

    /*public function testGetThemePhotoUrl(){
        $themeModel = D('theme');
        $photoUrl=$themeModel->getThemePhotoUrl(4);
        dump($photoUrl);
    }*/

    /*public function testGetAllCarouselPhotosUrl(){
        $shopModel=D('shop');
        $res=$shopModel->getAllDescPhotosUrl(24);
        dump($res);
    }*/

    /*public function testGetAllCarouselPhotoIds(){
        $shopModel=D('shop');
        $res=$shopModel->getAllCarouselPhotoIds(28);
        $idArr = explode(';', $res);
        dump($idArr);
    }*/

    /*public function test(){
        $themeIds=M('themeShop')->where(array('shop_id'=>27))->field('theme_id')->select();
        $themeModel=D('theme');
        $themeStr='';
        foreach($themeIds as $themeId){
            $themeName=$themeModel->getThemeNameByThemeId($themeId['theme_id']);
            if(empty($themeStr)){
                $themeStr=$themeStr.$themeName;
            }else{
                $themeStr=$themeStr.';'.$themeName;
            }
        }
    }*/

    /*public function testFindShopByShopId(){
        $res=D('shop')->findShopByShopId(25);
        p($res);
    }*/
}