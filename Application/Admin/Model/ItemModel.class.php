<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/18
 * Time: 9:42
 */

namespace Admin\Model;
use Think\Model\RelationModel;

class ItemModel extends RelationModel{



    /**
     * @param $title
     * @param $name
     * @param $photoUrl
     * @return mixed
     * 添加商品到数据库
     */
    public function doAddGoods($name, $desc, $price, $shop_id, $photoId){
        $data['item_name'] = $name;
        $data['desc'] = $desc;
        $data['price'] = $price;
        $data['shop_id'] = $shop_id;
        $data['item_photo_url'] = $photoId;
        $data['addTime'] = getCurrDateStr();
        return $this->add($data);
    }

    public function doEditGoods($item_id, $name, $desc, $price, $shop_id,$photoUrl){
        $data['item_name'] = $name;
        $data['desc'] = $desc;
        $data['price'] = $price;
        $data['shop_id'] = $shop_id;
        $data['item_photo_url'] = $photoUrl;
        $data['addTime'] = getCurrDateStr();
        return $this->where(array("item_id"=>$item_id))->save($data);
    }

    public function getGoodsPhotoUrl($itemId){
        return $this->where(array("item_id"=>$itemId))->getField('item_photo_url');
    }

    public function doDelGoods($itemId){
        return $this->where(array("item_id"=>$itemId))->delete();
    }

    public function findGoodsByGoodsId($itemId){
        return $this->where(array("item_id"=>$itemId))->find();
    }
    

}