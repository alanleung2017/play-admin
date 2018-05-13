<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/19
 * Time: 0:04
 */

namespace Home\Model;
use Think\Model;

class ItemModel extends Model
{
    //商品表自动验证
    protected $_validate = array(
        //-1,'长度不合法！'
        array('name', '', -1, self::EXISTS_VALIDATE,'notequal'),


    );

    //商品表自动完成
    protected $_auto = array(
        array('addTime', 'time', self::MODEL_INSERT, 'function'),
    );
    /**
     * @param $postData
     * @return mixed
     * 添加单品到数据库
     */
    public function doAddItem($shop_id,$item_photo_url,$item_name,$desc,$price){
        $data = array(
            'shop_id'=>$shop_id,
            'item_photo_url'=>$item_photo_url,
            'item_name'=>$item_name,
            'desc'=>$desc,
            'price'=>$price,
        );

        if ($this->create($data)) {
            $uid = $this->add();
            return $uid ? $uid : 0;
        } else {
            return $this->getError();
        }

    }

    /**
     * @param $itemName
     * @return mixed
     * 根据单品名字查找一条单品记录
     */
    public function findItemByItemName($itemName){
        return $this->where(array('item_name'=>$itemName))->find();
    }

    /**
     * @param $pageNo
     * @param $pageSize
     * @return mixed
     * 分页查询
     */
    public function findItemsByPage($pageNo, $pageSize){
        $offset = ($pageNo-1)*$pageSize; //起始行
        return $this->order('addTime desc')->limit($offset, $pageSize)->select();
    }
}