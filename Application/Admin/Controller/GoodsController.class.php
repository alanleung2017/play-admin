<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/1/14
 * Time: 12:19
 */

namespace Admin\Controller;

class GoodsController extends AuthController{

    public function index(){
        $this->assign('AllTheme', $this->getALLTheme());
        $this->display('indexNew');
    }

    public function goodsViewer(){
        if(empty(I('get.isSearch'))){
            $count = M('item')->count("item_id");
//        dump($count);exit;
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('goods','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里
            $list = M('item')->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
//            dump($list);exit;
            $photo = D('photos');
            $shop = D('shop');
            for($i=0; $i<count($list); $i++){
                $photoUrl=$photo->getPhotoUrlByID($list[$i]['item_photo_url']);
                $list[$i]['item_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            }
            for($i=0; $i<count($list); $i++){
//                p($list[$i]['shop_id']);
                $shop_name=$shop->findShopByShopId($list[$i]['shop_id']);
                $list[$i]['shop_name']=$shop_name['shop_name'];
            }
            $this->assign('goodsList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->display('goodsList');
        }else{

            $condition=I('get.searchGoodsName');
            $count = M('item')->where(array('item_name'=>$condition))->count("item_id");
//        dump($count);exit;
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('item','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里
            $_where['item_name']=array('like','%'.$condition.'%');
            $list = M('item')->where($_where)->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            for($i=0; $i<count($list); $i++){
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['item_photo_url']);
                $list[$i]['item_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            }
//        dump($list);exit;
            $this->assign('goodsList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->assign('searchGoodName',$condition);
            $this->display('goodsList');
        }
    }


    //http://www.thinkphp.cn/code/685.html
    //http://www.thinkphp.cn/topic/15565.html
    /**
     * 上传多张图片
     */
    public function uploadMany() {
        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    10485760, // 设置附件上传大小10M
                'savePath'   =>    'Goods/', // 设置上传目录
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
//                        $this->error('图片上传失败，数据库连接失败或插入数据非法');
                    }
//                    echo $o;
                    $arr['status']  = 1;
                    $arr['content'] = $o;
                    $this->ajaxReturn($arr);
                }
            }
        }
    }


    /*
     * 上传商品图片
     * */
    public function uploadGoodsPic(){
        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    10485760, // 设置附件上传大小10M
                'savePath'   =>    'Goods/', // 设置上传目录
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
                    $data['addGoods'] = getCurrDateStr();
                    $o = M('photos')->add($data);
                    $arr['photo_url'] = C('PIC_URL_PREFIX').$data['photo_url'];
                    if(false === $o){
                        $arr['status']  = 0;
                        $arr['content'] = '图片上传失败，数据库连接失败或插入数据非法';
                        $this->ajaxReturn($arr);
//                        $this->error('图片上传失败，数据库连接失败或插入数据非法');
                    }
//                    echo $o;
                    $arr['status']  = 1;
                    $arr['content'] = $o;
                    $this->ajaxReturn($arr);
                }
            }
        }
    }

    /*
     * 获取所有的主题
     * */
    public function getALLTheme(){
        $theme = D('theme');
        $res = $theme->getALLTheme();
        return $res;
    }

    /*
    * 获取指定主题下的所有特色店
    * */
    public function getShop()
    {
        $shop = D('shop');
        $theme_id = I('post.theme_id');
        $res = $shop->getShopByThemeId($theme_id);
        $this->ajaxReturn($res);
        //$this->ajaxReturn(json_encode($res,JSON_UNESCAPED_UNICODE));
       // return $res;
    }


    /**
     * 添加商品
     */
    public function addGoods(){
        $goods = D("item");
        $name = I('post.goods_name');
        if(empty($name)){
            $this->error('添加商品失败，商品名称不能为空！');
        }
        $desc = I('post.desc');
        if(empty($desc)){
            $this->error('添加商品失败，商品简介不能为空！');
        }
        $price = I('post.price');
        if(empty($price)){
            $this->error('添加商品失败，商品价格不能为空！');
        }
        $shop_id = I('post.shop_id');
        if(empty($shop_id)){
            $this->error('添加商品失败，商品所属特色店不能为空！');
        }
        $photo_id=I('post.goods_photo_id');
        if(empty($photo_id)){
            $this->error('添加商品失败，请上传商品图片！');
        }
        $photos=D('photos');
        $photoUrl=$photos->getPhotoUrlByID($photo_id);
        $optionRes = $goods->doAddGoods($name, $desc, $price, $shop_id, $photo_id);
        if($optionRes){
            $this->success('添加商品成功！');
        } else {
            $this->error('添加商品失败，数据库连接失败或插入数据非法！');
        }
    }

    /*
     * 修改商品信息
     * */
    public function editGoods(){
        if(IS_GET){
            $itemId=I("get.itemId");
            $itemModel=D('item');
            $res=$itemModel->findGoodsByGoodsId($itemId);

            $res['old_item_photo_url']=$res['item_photo_url'];
            $photoUrl=D('photos')->getPhotoUrlByID($res['item_photo_url']);
            $res['item_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            $this->assign('GoodsInfo',$res);
            $this->display('newEditGoods');
        }else{
            $theme = D("item");
            $name = I('post.goods_name');
            if(empty($name)){
                $this->error('修改商品信息失败，商品名称不能为空！');
            }
            $desc = I('post.desc');
            if(empty($desc)){
                $this->error('修改商品信息失败，商品简介不能为空！');
            }
            $price = I('post.price');
            if(empty($price)){
                $this->error('修改商品信息失败，商品价格不能为空！');
            }
            $shop_id = I('post.shop_id');
            if(empty($shop_id)){
                $this->error('添加商品信息失败，商品所属商店ID获取失败！');
            }
            $photo_id=I('post.item_photo_id');
            /*if(empty($photo_id)){
                $this->error('添加主题失败，请上传主题图片！');
            }*/
            if(!empty($photo_id)){
//                $photos=D('photos');
//                $photoUrl=$photos->getPhotoUrlByID($photo_id);
                $photoUrl=$photo_id;
            }else{
                $photoUrl=I('post.old_item_photo_url');
            }

            $optionRes = $theme->doEditGoods(I('post.goods_id'),$name, $desc, $price, $shop_id,$photoUrl);
            if($optionRes){
                $this->success('修改商品信息成功！');
            } else {
                $this->error('修改商品信息失败，数据库连接失败或插入数据非法！');
            }
        }
    }

    /*
     * 删除商品
     * */
    public function delGoods($itemId){
        $goods = D('item');
        $photos=D('photos');
        $photoUrl=$goods->getGoodsPhotoUrl($itemId);
        $goods->startTrans();
        $option1=$goods->doDelGoods($itemId);
//        dump($option1);exit;
//        dump($themeId);exit;
//        dump($photoUrl);exit;
        $option2=$photos->delPhotoById($photoUrl);
//        dump($option2);exit;
        if($option1 && $option2){
            $goods->commit();//成功则提交
            $this->success('删除商品成功！');
        } else {
            $goods->rollback();//不成功，则回滚
            $this->error('删除商品失败，数据库连接失败！');
        }
    }
}