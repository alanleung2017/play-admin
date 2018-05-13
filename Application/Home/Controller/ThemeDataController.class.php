<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/15
 * Time: 23:47
 */

namespace Home\Controller;
use Common\Common\Utils\Response;
use Think\Controller;

class ThemeDataController extends Controller
{
    /**
     * @param int $from
     * @param int $pageSize
     * 获取主题列表接口
     */
    public function themeList($from=1, $pageSize=6){
        $theme = D('theme');
        $themes = $theme->findThemesInfoByPageNo($from, $pageSize);
        $wanted=array();
        $i=0;
        foreach ($themes as $t) {
            foreach ($t as $k=>$v) {
                if($k == 'theme_photo_url'){
                    $url=D('photos')->getPhotoUrlByID($v);
                    $wanted[$i][$k] = C('PIC_URL_PREFIX').$url;
                } else{
                    $wanted[$i][$k] = $v;
                }
            }
            $i++;
        }
        if(!empty($wanted)){
            return Response::show(200, '主题列表获取成功', $wanted);
        } else{
            return Response::show(400, '主题列表获取失败', $wanted);
        }
    }

    /**
     * @param int $from
     * @param int $pageSize
     * 传递主题ID，获取该主题下的所有特色店以及主题简介
     */
    public function ThemeShops(){
        $theme = D('shop');
        $result = $theme->getShop(I('get.themeID'));
        if(!empty($result)){
            return Response::show(200, '主题特色店获取成功', $result);
        } else{
            return Response::show(400, '主题特色店获取失败', $result);
        }

    }

}
