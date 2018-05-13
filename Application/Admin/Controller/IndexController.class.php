<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/1/13
 * Time: 19:15
 */

namespace Admin\Controller;

class IndexController extends AuthController{

    public function index(){
        $this->display('indexNew');
    }

    public function themeViewer(){
        if(empty(I('get.isSearch'))){
            $count = M('theme')->count("theme_id");
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
            $list = M('theme')->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            for($i=0; $i<count($list); $i++){
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['theme_photo_url']);
                $list[$i]['theme_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            }
//        dump($list);exit;
            $this->assign('themeList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->display('themeList');
        }else{
            $condition=I('get.searchCondition');
            $_where['theme_title']=array('like','%'.$condition.'%');
            $count = M('theme')->where($_where)->count("theme_id");
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

            $list = M('theme')->where($_where)->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            for($i=0; $i<count($list); $i++){
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['theme_photo_url']);
                $list[$i]['theme_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            }
//        dump($list);exit;
            $this->assign('themeList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->assign('searchCondition',$condition);
            $this->display('themeList');
        }
    }

    /**
     * @param $themeTitle
     * 检查主题是否存在
     */
    public function isThemeExists($themeTitle){
        $theme = D("theme");
        if ($theme->isThemeExists($themeTitle)) {
            $data['msg'] = true;
        } else {
            $data['msg'] = false;
        }
        $this->ajaxReturn($data);
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
                'savePath'   =>    'Theme/', // 设置上传目录
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

    public function uploadThemePic(){
        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    10485760, // 设置附件上传大小10M
                'savePath'   =>    'Theme/', // 设置上传目录
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
     * 添加主题
     */
    public function addTheme(){
        $theme = D("theme");
        $title = I('post.theme_title');
        if(empty($title)){
            $this->error('添加主题失败，主题标题不能为空！');
        }
        $desc = I('post.desc');
        if(empty($desc)){
            $this->error('添加主题失败，主题描述不能为空！');
        }
        $type = I('post.theme_type');
        if(empty($type)){
            $this->error('添加主题失败，主题类型不能为空！');
        }
        $photo_id=I('post.theme_photo_id');
        if(empty($photo_id)){
            $this->error('添加主题失败，请上传主题图片！');
        }
        $photos=D('photos');
//        $photoUrl=$photos->getPhotoUrlByID($photo_id);
        $optionRes = $theme->doAddTheme($title, $desc, $type, $photo_id);
        if($optionRes){
            $this->success('添加主题成功！');
        } else {
            $this->error('添加主题失败，数据库连接失败或插入数据非法！');
        }
    }

    public function editTheme(){
        if(IS_GET){
            $themeId=I("get.themeId");
            $themeModel=D('theme');
            $res=$themeModel->findThemeByThemeId($themeId);
//            dump($res);exit;
            $res['old_theme_photo_url']=$res['theme_photo_url'];
            $photoUrl=D('photos')->getPhotoUrlByID($res['theme_photo_url']);
            $res['theme_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            $this->assign('themeInfo',$res);
            $this->display('newEditTheme');
        }else{
            $theme = D("theme");
            $title = I('post.theme_title');
            if(empty($title)){
                $this->error('添加主题失败，主题标题不能为空！');
            }
            $desc = I('post.desc');
            if(empty($desc)){
                $this->error('添加主题失败，主题描述不能为空！');
            }
            $type = I('post.theme_type');
            if(empty($type)){
                $this->error('添加主题失败，主题类型不能为空！');
            }
            $photo_id=I('post.theme_photo_id');
            /*if(empty($photo_id)){
                $this->error('添加主题失败，请上传主题图片！');
            }*/
            if(!empty($photo_id)){
//                $photos=D('photos');
//                $photoUrl=$photos->getPhotoUrlByID($photo_id);
                $photoUrl=$photo_id;
            }else{
                $photoUrl=I('post.old_theme_photo_url');
            }

            $optionRes = $theme->doEditTheme(I('post.theme_id'),$title, $desc, $type, $photoUrl);
            if($optionRes){
                $this->success('修改主题成功！');
            } else {
                $this->error('修改主题失败，数据库连接失败或插入数据非法！');
            }
        }
    }

    //事务demo需要表引擎为Innodb
    /*$m=D('YourModel');//或者是M();
    $m2=D('YouModel2');
    $m->startTrans();//在第一个模型里启用就可以了，或者第二个也行
    $result=$m->where('删除条件')->delete();
    $result2=m2->where('删除条件')->delete();
    if($result && $result2){
    $m->commit();//成功则提交
    }else{
        $m->rollback();//不成功，则回滚
    }*/
    public function delTheme($themeId){
        $themeModel = D('theme');
        $photosModel=D('photos');
        $photoUrl=$themeModel->getThemePhotoUrl($themeId);
        $themeModel->startTrans();
        $option1=$themeModel->doDelTheme($themeId);
//        dump($option1);exit;
//        dump($themeId);exit;
//        dump($photoUrl);exit;
        $option2=$photosModel->delPhotoById($photoUrl);
//        dump($option2);exit;
        if($option1 && $option2){
            $themeModel->commit();//成功则提交
            $this->success('删除主题成功！');
        } else {
            $themeModel->rollback();//不成功，则回滚
            $this->error('删除主题失败，数据库连接失败！');
        }
    }

    public function themeShop($themeId){
        if(IS_GET){
            $showName=D('theme')->getThemeNameById($themeId);
            $this->assign('showName',$showName);
            $shopIds=M('themeShop')->where(array("theme_id"=>$themeId))->field('shop_id')->select();
            $tmpArr=array();
            $i=0;
            foreach ($shopIds as $id) {
                $tmpArr[$i]=$id["shop_id"];
                $i++;
            }
            if(empty($tmpArr)){
                $this->assign('shopList', '');
            }else{
                $_where["shop_id"]=array("in",$tmpArr);
                $count = M('shop')->where($_where)->count("shop_id");
//            dump(M('shop')->getLastSql());exit;
//                dump($count);exit;
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
                $list = M('shop')->where($_where)->limit($Page->firstRow . ',' . $Page->listRows)
                    ->order('addTime desc')->select();
//            dump(M('shop')->getLastSql());exit;
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
            }
            $this->display('newThemeShop');
        }
    }

}