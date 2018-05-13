<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/1/14
 * Time: 12:19
 */

namespace Admin\Controller;

class ShopController extends AuthController{

    public function index(){
        $this->assign('allTheme',$this->getAllTheme());
//        dump($this->getAllTheme());exit;
        $this->display('indexNew');
    }

    /*
     * 获取所有主题
     * */
    private function getAllTheme(){
        $themeModel = D("theme");
        $res = $themeModel->getAllTheme();
        return $res;
    }

    public function shopViewer(){
        if(empty(I('get.isSearch'))){
            $count = M('shop')->count("shop_id");
//        dump($count);exit;
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里
//            $show = $Page->show();
            $list = M('shop')->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
//            dump($list);exit;
            for($i=0; $i<count($list); $i++){
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['shop_photo_url']);
                $list[$i]['shop_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
                $shopModel=D('shop');
                $res=$shopModel->getAllCarouselPhotosUrl($list[$i]['shop_id']);
//                dump($res);exit;
                if(empty($res)){
                    $list[$i]['carousel_photos']='';
                }else{
                    $list[$i]['carousel_photos']=$res;
                }

                $res2=$shopModel->getAllDescPhotosUrl($list[$i]['shop_id']);
                if(empty($res2)){
                    $list[$i]['desc_photos']='';
                }else{
                    $list[$i]['desc_photos']=$res2;
                }

                $themeIds=M('themeShop')->where(array('shop_id'=>$list[$i]['shop_id']))->field('theme_id')->select();
                $themeModel=D('theme');
                $themeStr='';
                foreach($themeIds as $themeId){
                    $themeName=$themeModel->getThemeNameByThemeId($themeId['theme_id']);
                    if(empty($themeStr)){
                        $themeStr=$themeStr.$themeName;
                    }else{
                        $themeStr=$themeStr.'；'.$themeName;
                    }
                }
                $list[$i]['themeStr']=$themeStr;
            }
//            dump($list);exit;
            $this->assign('shopList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->display("shopList");
        }else{
            $condition=I('get.searchCondition');
            $_where['shop_name']=array('like','%'.$condition.'%');
            $count = M('shop')->where($_where)->count("shop_id");
//        dump($count);exit;
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里

            $list = M('shop')->where($_where)->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
//            dump(M('shop')->getLastSql());
            for($i=0; $i<count($list); $i++){
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['shop_photo_url']);
                $list[$i]['shop_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
                $shopModel=D('shop');
                $res=$shopModel->getAllCarouselPhotosUrl($list[$i]['shop_id']);
                if(empty($res)){
                    $list[$i]['carousel_photos']='';
                }else{
                    $list[$i]['carousel_photos']=$res;
                }

                $res2=$shopModel->getAllDescPhotosUrl($list[$i]['shop_id']);
                if(empty($res2)){
                    $list[$i]['desc_photos']='';
                }else{
                    $list[$i]['desc_photos']=$res2;
                }

                $themeIds=M('themeShop')->where(array('shop_id'=>$list[$i]['shop_id']))->field('theme_id')->select();
                $themeModel=D('theme');
                $themeStr='';
                foreach($themeIds as $themeId){
                    $themeName=$themeModel->getThemeNameByThemeId($themeId['theme_id']);
                    if(empty($themeStr)){
                        $themeStr=$themeStr.$themeName;
                    }else{
                        $themeStr=$themeStr.'；'.$themeName;
                    }
                }
                $list[$i]['themeStr']=$themeStr;
            }
//            dump($list);exit;
            $this->assign('shopList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->assign('searchCondition',$condition);
            $this->display("shopList");
        }
    }

    /**
     * @param $shopName
     * 检查商店是否存在
     */
    public function isShopExists($shopName){
        $shopModel = D("shop");
        if ($shopModel->isShopExists($shopName)) {
            $data['msg'] = true;
        } else {
            $data['msg'] = false;
        }
        $this->ajaxReturn($data);
    }

    public function uploadShopPic(){
        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    10485760, // 设置附件上传大小10M
                'savePath'   =>    'Shop/', // 设置上传目录
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

    /**
     * 上传多张图片
     */
    public function uploadMany() {
        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    10485760, // 设置附件上传大小10M
                'savePath'   =>    'Shop/', // 设置上传目录
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
                    }
//                    echo $o;
                    $arr['photo_url'] = C('PIC_URL_PREFIX').$data['photo_url'];
                    $arr['status']  = 1;
                    $arr['content'] = $o;
                    $this->ajaxReturn($arr);
                }
            }
        }
    }

    /**
     * 添加特色店
     */
    public function addShop(){
        if(empty(I('post.desc'))){
            $this->error("商店简介不能为空！");
        }
        if(empty(I('post.location'))){
            $this->error("商店地址不能为空！");
        }
        if(empty(I('post.theme'))){
            $this->error("商店主题不能为空！");
        }
        $theme=implode(',',I('post.theme'));
        if(empty(I('post.shop_photo_url'))){
            $this->error("商店图片不能为空！");
        }
        if(empty(I('post.carousel_photo_url_ids'))){
            $this->error("轮播图片不能为空！");
        }
        $str1 = "-1";
        $cids = I('post.carousel_photo_url_ids');
        foreach($cids as $id){
            $str1 = $str1.';'.$id;
        }
//        dump($str1);exit;
        if(empty(I('post.desc_photo_url_ids'))){
            $this->error("描述图片不能为空！");
        }
        $str2 = "-1";
        $dids = I('post.desc_photo_url_ids');
        foreach($dids as $id){
            $str2 = $str2.';'.$id;
        }

        $shop = D('shop');
        $result = $shop->doAddShop(I('post.shop_name'),I('post.desc'),I('post.location'),
            $theme,I('post.detail'),I('post.shop_photo_url'),$str1,$str2);
        if($result > 0){
            $this->success('添加特色店成功！');
        } else {
            switch($result){
                case -1 :  $this->error('添加失败，店名不得为空！');break;
                case -2 :  $this->error('添加失败，店名长度超出范围！');break;
                case -3 :  $this->error('添加失败，店名被占用！');break;
                case -4 :  $this->error('添加失败，店铺网址不能为空！');break;
                case -5 :  $this->error('添加失败，店铺网址格式错误！');break;
                default: $this->error('添加失败，未知错误！');
            }
        }
    }

    public function delShop($shopId){
        $shopModel = D('shop');
        $photosModel=D('photos');

        $tmp=$shopModel->getAllCarouselPhotoIds($shopId);
        $idArr = explode(';', $tmp);
        foreach ($idArr as $id) {
            if($id > 0){
                $photosModel->delPhotoById($id);
            }
        }

        $tmp2=$shopModel->getAllDescPhotoIds($shopId);
        $idArr2 = explode(';', $tmp2);
        foreach ($idArr2 as $did) {
            if($did > 0){
                $photosModel->delPhotoById($did);
            }
        }

        $photoUrl=$shopModel->getShopPhotoUrl($shopId);
//        dump($photoUrl);exit;
        $shopModel->startTrans();
        $option1=$shopModel->doDelShop($shopId);
//        dump($option1);exit;
        $option2=$photosModel->delPhotoById($photoUrl);
//        dump($option2);exit;
        $option3=M('themeShop')->where(array('shop_id'=>$shopId))->delete();
        if($option1 && $option2 && $option3){
            $shopModel->commit();//成功则提交
        } else {
            $shopModel->rollback();//不成功，则回滚
            $this->error("删除商店失败，数据库连接错误！");
        }

        $this->success("删除商店成功");
    }

    public function editShop(){
        if(IS_GET){
            $shopId=I("get.shopId");
            $shopModel=D('shop');
            $res=$shopModel->findShopByShopId($shopId);
//            dump($res);exit;
            $res['o_carousel']=$res['carousel_photo_url_ids'];
            $res['o_desc']=$res['desc_photo_url_ids'];
            $res['o_shop_photo_url']=$res['shop_photo_url'];
            $shop_photo=D('photos')->getPhotoUrlByID($res['shop_photo_url']);
            $res['shop_photo_url']=C('PIC_URL_PREFIX').$shop_photo;

            $themeIds=M('themeShop')->where(array('shop_id'=>$shopId))->field('theme_id')->select();
            $themeModel=D('theme');
            $themeStr='';
            foreach($themeIds as $themeId){
                $themeName=$themeModel->getThemeNameByThemeId($themeId['theme_id']);
                if(empty($themeStr)){
                    $themeStr=$themeStr.$themeName;
                }else{
                    $themeStr=$themeStr.'；'.$themeName;
                }
            }
            $res['themeStr']=$themeStr;
            $this->assign('shopInfo',$res);
            $this->assign('allTheme',$this->getAllTheme());
            $carouselUrls=$shopModel->getAllCarouselPhotosUrl($shopId);
            $this->assign('carouselUrls',$carouselUrls);
            /*$this->assign('carousel1',$carouselUrls[0]);
            $this->assign('carousel2',$carouselUrls[1]);
            $this->assign('carousel3',$carouselUrls[2]);*/
            $descUrls=$shopModel->getAllDescPhotosUrl($shopId);
//            dump($descUrls);exit;
            $this->assign('descUrls',$descUrls);
            $this->display('newEditShop');
        }else{
            if(empty(I('post.shop_name'))){
                $this->error("商店名称不能为空！");
            }
            if(empty(I('post.desc'))){
                $this->error("商店简介不能为空！");
            }
            if(empty(I('post.location'))){
                $this->error("商店地址不能为空！");
            }
            if(empty(I('post.theme'))){
                $this->error("商店主题不能为空！");
            }
            $theme=implode(',',I('post.theme'));
            if(empty(I('post.detail'))){
                $this->error("详情页不能为空！");
            }

            $photoUrl='';
            if(!empty(I('post.shop_photo_url'))){
                $photoUrl=I('post.shop_photo_url');
            }else{
                $photoUrl=I('post.o_shop_photo_url');
            }

            $cids = I('post.carousel_photo_url_ids');
            $carousel='';
            if(!empty($cids)){
                $str1 = "-1";
                foreach($cids as $id){
                    $str1 = $str1.';'.$id;
                }
                $carousel=$str1;
            }else{
                $carousel=I('post.o_carousel');
            }

            $dids = I('post.desc_photo_url_ids');
            $desc='';
            if(!empty($dids)){
                $str2 = "-1";
                foreach($dids as $id){
                    $str2 = $str2.';'.$id;
                }
                $desc=$str2;
            }else{
                $desc=I('post.o_desc');
            }

            $shop = D('shop');
            $result = $shop->doEditShop(I('post.shop_id'),I('post.shop_name'),I('post.desc'),I('post.location'),
                $theme, I('post.detail'), $photoUrl, $carousel, $desc);
            if($result > 0){
                $this->success('修改特色店成功！');
            } else {
                $this->success('修改特色店失败，数据库连接错误！');
            }
        }
    }
}