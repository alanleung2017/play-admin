<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/15
 * Time: 23:36
 */

namespace Home\Controller;
use Think\Controller;
use Common\Common\Utils\Response;

class ShopDataController extends Controller
{
    /**
     * @param int $pageNo 页码
     * @param int $pageSize 页大小（每页多少条数据）
     * app首页数据接口
     * 主页每日推荐数据（图片）获取推荐，推荐是特色店，分页，每页5个
     */
    public function DailyShops($pageNo=1, $pageSize=5){
        $shop = D('shop');
        $shops = $shop->findShopsInfoByPageNo($pageNo, $pageSize);
//        dump($shops);
        $wanted=array();
        $i=0;
        foreach($shops as $shop){
            foreach($shop as $k=>$v){
                if($k == 'shop_photo_url'){
                    $url=D('photos')->getPhotoUrlByID($v);
                    $wanted[$i][$k]=C('PIC_URL_PREFIX').$url;
                } else{
                    $wanted[$i][$k]=$v;
                }
            }
            $i++;
        }

        if(!empty($shops)){
            return Response::show(200, '首页数据获取成功', $wanted);
        } else {
            return Response::show(400, '首页数据获取失败，请检查URL参数', $wanted);
        }
    }

    public function ShopPhotos($ShopID){
        if(!is_numeric($ShopID)){
            return Response::show(400, '轮播图片集获取失败，特色店id不是阿拉伯数字', array());
        }
        $shop=D('shop');
        $idArr = $shop->getCarouselPhotos($ShopID);
        if($idArr[0] == ''){
            $idArr=array();
        }
        $photos['photo_url'] = array();
        $photo = D('photos');
        foreach($idArr as $id){
            array_push($photos['photo_url'], C('PIC_URL_PREFIX').$photo->getPhotoUrlByID($id));
        }
        $data=array('photos'=>$photos);
        if(!empty($idArr)){
            return Response::show(200, '轮播图片集获取成功', $data);
        } else {
            return Response::show(400, '轮播图片集获取失败，请检查URL参数', $data);
        }
    }

    /**
    *2016.12.1——陈业胜
     ** 通过传递页码参数获取热门特色店，每页10条
     */
    public function HotShops(){
        $hotShop = D('shop');
        $result = $hotShop->getHotShop(I('get.from'));
        if(!empty($result)){
            return Response::show(200, '热门热色店获取成功', $result);
        } else {
            return Response::show(400, '热门热色店获取失败，请检查URL参数', $result);
        }
    }

    /**
     *2017.3.6——陈业胜
     ** 通过传热门特色店ID，返回detail字段
     */
    public function ShopInfo(){
        $shopInfo = D('shop');
        $result = $shopInfo->getShopInfo(I('get.ShopID'));
        if(!empty($result)){
            return Response::show(200, '店铺网址获取成功！', $result);
        } else {
            return Response::show(400, '店铺网址获取失败，请检查URL参数', $result);
        }
    }

}