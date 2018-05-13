<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/5/3
 * Time: 15:56
 */

namespace Home\Controller;
use Think\Controller;
use Common\Common\Utils\Response;

class CommonController extends Controller {
    public function search($word){
        if(empty($word)){
            return Response::show(400, '搜索失败：搜索条件为空', array());
        } else{
            $shopArr=D('Shop')->findShopByName($word);
            $themeArr=D('Theme')->findThemeByName($word);
            if(!empty($themeArr)){
                $res['theme']=$themeArr;
            }
            if(!empty($shopArr)){
                $res['shop']=$shopArr;
            }
            return Response::show(200, '成功获取搜索结果', $res);
        }
    }
}