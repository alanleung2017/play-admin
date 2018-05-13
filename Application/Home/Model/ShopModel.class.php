<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/11/10
 * Time: 17:24
 */

namespace Home\Model;
use Faker\Provider\tr_TR\DateTime;
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
    protected $_link = array(
        'theme_link_shop'=>array(
            'mapping_type' =>  self::MANY_TO_MANY,
            'class_name'=>'theme',
            'foreign_key'=>'shop_id',
            'mapping_fields'=>'theme_id, theme_title, theme_desc',
            'relation_foreign_key'  =>  'theme_id',
            'relation_table'    =>  'db_theme_shop'

        ),
    );

    //新增特色店数据
    public function doAddShop($name,$location,$photo_url,$desc,
                              $theme,$detail,$carousel_photos,$desc_photos){
        $data = array(
            'shop_name'=>$name,
            'location'=>$location,
            'shop_photo_url'=>$photo_url,
            'desc'=>$desc,
            'detail'=>$detail,
            //'theme_id'=>$theme,
            'carousel_photo_url_ids'=>$carousel_photos,
            'desc_photo_url_ids'=>$desc_photos,
            'addTime'=>date('YmdHis',time()),
//            'theme_link_shop'=> array(
//                'theme_id'=>$theme,
//            )
        );
        $theme_arr = explode(',',$theme);
        if ($this->create($data)) {
            $result = $this->add($data);
            if($result > 0){
                $themeShop = M('ThemeShop');
                for($i=0;$i<count($theme_arr);$i++) {
                    $data1 = array(
                        'shop_id' => $result,
                        'theme_id' => $theme_arr[$i],
                    );
                    $themeShop->add($data1);
                }
            }
            return $result ? $result : 0;
        } else {
            return $this->getError();
        }
    }

    /**
     * @param $themeName
     * 通过主题Id获取所属特色店
     */
    public function getShop($themeId){
        $map['theme_id'] = $themeId;
        $theme_shop = M('theme_shop');
        $count = $theme_shop->where($map)->select();
        if($count == null){
            return ;
        }
        $shop_id = $theme_shop->field('shop_id')->where($map)->select();
        for($i = 0; $i < count($shop_id); $i++){
            $deal[] = $shop_id[$i]['shop_id'];
        }
        $shop_id = implode(',',$deal);
        $mat['shop_id'] = array('in',$shop_id );
        $shop = $this->relation(true)->where($mat)->select();

        if($shop) {
            foreach ($shop as $_value) {
                $desc = $_value['theme_link_shop'];
                foreach ($desc as $_value) {
                    $shop1 = $_value['theme_desc'];
                }
                break;
            }
            $deal = array();
            $i = 0;
            foreach($shop as $_value){
                $deal[$i]['shop_id'] = $_value['shop_id'];
                $deal[$i]['shop_name'] = $_value['shop_name'];
                $deal[$i]['desc'] = $_value['desc'];
                $deal[$i]['location'] = $_value['location'];
                $url=D('photos')->getPhotoUrlByID($_value['shop_photo_url']);
                $deal[$i]['shop_photo_url'] =  C('PIC_URL_PREFIX').$url;
                $i++;
            }
            $result = array(
                'themeBrief'=>$shop1,
                'shops' => $deal,
            );
            return $result;
        }else{
            return ;
        }
    }

    /**
     * @param $themeName
     * /**2016.12.1——陈业胜
     * 返回热门的特色店，按照收藏数量递减排序，每页十个
     */
    public function getHotShop($from){
        $result = $this->field('shop_id,shop_photo_url,shop_name,desc')->order('collection desc')->page($from.',10')->select();
        for($i=0; $i<count($result); $i++){
            $url=D('photos')->getPhotoUrlByID($result[$i]['shop_photo_url']);
            $result[$i]['shop_photo_url']=C('PIC_URL_PREFIX').$url;
        }
//        dump($result);
        return $result;
    }

    /**
     * @param $themeName
     * /**2017.3.6——陈业胜
     * 返回特色店店铺网址
     */
    public function getShopInfo($shopId){
        $map['shop_id'] = $shopId;
        $result = $this->field('detail')->where($map)->select();
        return $result;
    }


    /**
     * @param $pageNo
     * @param $pageSize
     * @return mixed
     * 分页查询
     */
    public function findShopsInfoByPageNo($pageNo, $pageSize){
        $offset = ($pageNo-1)*$pageSize; //起始行
        return $this->field('shop_id,shop_photo_url,shop_name,desc')->order('addTime desc')->limit($offset, $pageSize)->select();
    }

    /**
     * @return mixed
     * 获取特色店id和店名结果集
     */
    public function getShopsIdAndshopsName(){
        return $this->field('shop_id,shop_name')->select();
    }

    public function getCarouselPhotos($shopID){
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
        return $idArr;
    }

    public function findShopByName($word){
        $map['shop_name']=array('like','%'.$word.'%');
        $res=$this->where($map)->field('shop_name,shop_photo_url,shop_id,desc')->select();
        $i=0;
        foreach ($res as $item) {
            $res[$i]['shop_photo_url']=C('PIC_URL_PREFIX').D('Photos')->getPhotoUrlByID($item['shop_photo_url']);
            ++$i;
        }
//        p($res);exit;
        return $res;
    }
}