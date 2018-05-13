<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/10
 * Time: 17:24
 */

namespace Admin\Model;
use Think\Model\RelationModel;

class ShopModel extends RelationModel{
    //特色店表自动验证
    protected $_validate = array(
        //-1,店名不能为空
        array('shop_name','require',-1), //默认情况下用正则进行验证
        //-2,'店名长度不合法！'
        array('shop_name', '2,20', -2, self::EXISTS_VALIDATE,'length'),
        //-3,'店名被占用！'
        array('shop_name', '', -3, self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        //-4,店铺网址不能为空
        array('detail','require',-4), //默认情况下用正则进行验证
        //-5 '店家网址不合法'
        array('detail', 'url', -5),

    );

    //多对多关联主题
    /*protected $_link = array(
        'theme_link_shop'=>array(
            'mapping_type' =>  self::MANY_TO_MANY,
            'class_name'=>'theme',
            'foreign_key'=>'shop_id',
            'mapping_fields'=>'theme_id, theme_title, theme_desc',
            'relation_foreign_key'  =>  'theme_id',
            'relation_table'    =>  'db_theme_shop'

        ),
    );*/

    //新增特色店数据
    public function doAddShop($name,$desc,$location,$theme,
                              $detail,$photoUrl,$carousel_photos,$desc_photos){
        $currTime=getCurrDateStr();
        $data = array(
            'shop_name'=>$name,
            'desc'=>$desc,
            'location'=>$location,
            'shop_photo_url'=>$photoUrl,
            'detail'=>$detail,
            'carousel_photo_url_ids'=>$carousel_photos,
            'desc_photo_url_ids'=>$desc_photos,
            'addTime'=>$currTime
        );
        $theme_arr = explode(',',$theme);
        if ($this->create($data)) {
            $result = $this->add($data);
            if($result > 0){
                $themeShop = M('ThemeShop');
                for($i=0;$i<count($theme_arr);$i++) {
                    $data2 = array(
                        'shop_id' => $result,
                        'theme_id' => $theme_arr[$i],
                    );
                    $themeShop->add($data2);
                }
            }
            return $result ? $result : 0;
        } else {
            return $this->getError();//自动验证
        }
    }

    public function doEditShop($shopId,$name,$desc,$location,$theme,
                              $detail,$photoUrl,$carousel_photos,$desc_photos){
        $currTime=getCurrDateStr();
        $data = array(
            'shop_name'=>$name,
            'desc'=>$desc,
            'location'=>$location,
            'shop_photo_url'=>$photoUrl,
            'detail'=>$detail,
            'carousel_photo_url_ids'=>$carousel_photos,
            'desc_photo_url_ids'=>$desc_photos,
            'addTime'=>$currTime
        );
        $theme_arr = explode(',',$theme);
        $result = $this->where(array('shop_id'=>$shopId))->save($data);
        if($result > 0){
            $themeShop = M('ThemeShop');
            $themeShop->where(array('shop_id'=>$shopId))->delete();
            for($i=0;$i<count($theme_arr);$i++) {
                $data2 = array(
                    'shop_id' => $shopId,
                    'theme_id' => $theme_arr[$i],
                );
                $themeShop->add($data2);
            }
        }

        return $result;
    }

    public function getAllCarouselPhotosUrl($shopID){
        $arr = $this->where(array('shop_id'=>$shopID))->field('carousel_photo_url_ids')->select();
        $tmp = $arr[0]['carousel_photo_url_ids'];
        $idArr = explode(';', $tmp);
//        dump($idArr);
        foreach ($idArr as $k=>$v) {
            if($v < 0){
                array_splice($idArr, $k, 1);
            }
        }
//        dump($idArr);
        $i=0;
        $resArr=array();
        foreach($idArr as $id){
            $photosModel = D('photos');
            $tmpUrl=$photosModel->getPhotoUrlByID($id);
            $resArr[$i]=C('PIC_URL_PREFIX').$tmpUrl;
            ++$i;
        }
        return $resArr;
    }

    public function getAllDescPhotosUrl($shopID){
        $arr = $this->where(array('shop_id'=>$shopID))->field('desc_photo_url_ids')->select();
        $tmp = $arr[0]['desc_photo_url_ids'];
        $idArr = explode(';', $tmp);
//        dump($idArr);
        foreach ($idArr as $k=>$v) {
            if($v < 0){
                array_splice($idArr, $k, 1);
            }
        }
//        dump($idArr);
        $i=0;
        $resArr=array();
        foreach($idArr as $id){
            $photosModel = D('photos');
            $tmpUrl=$photosModel->getPhotoUrlByID($id);
            $resArr[$i]=C('PIC_URL_PREFIX').$tmpUrl;
            ++$i;
        }
        return $resArr;
    }

    /**
     * @param $shopName
     * @return bool
     * 检查商店是否存在
     */
    public function isShopExists($shopName)
    {
        //查询指定条件的记录个数
        $count = $this->where(array(
            'shop_name' => $shopName
        ))->count();

        // 返回
        return 1 == $count;
    }

    public function getShopByThemeId($themeId){
        $shop_id = M('theme_shop')->field('shop_id')->where(array("theme_id"=>$themeId))->select();
        foreach($shop_id as $key=>$value){
            foreach($value as $shopid){
                $shop[$key] = $shopid;
            }
        }
        $map['shop_id']=array('in',implode(',',$shop));
        return  $this->field('shop_id,shop_name')->where($map)->select();
    }

    public function getShopPhotoUrl($shopId){
        return $this->where(array("shop_id"=>$shopId))->getField('shop_photo_url');
    }

    public function getAllCarouselPhotoIds($shopID){
        $arr = $this->where(array('shop_id'=>$shopID))->getField('carousel_photo_url_ids');
        return $arr;
    }

    public function getAllDescPhotoIds($shopID){
        $arr = $this->where(array('shop_id'=>$shopID))->getField('desc_photo_url_ids');
        return $arr;
    }

    public function doDelShop($shopId){
        return $this->where(array("shop_id"=>$shopId))->delete();
    }

    public function findShopByShopId($shopId){
        return $this->where(array("shop_id"=>$shopId))->find();
    }
}