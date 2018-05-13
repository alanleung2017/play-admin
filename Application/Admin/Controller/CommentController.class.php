<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/1/14
 * Time: 12:19
 */

namespace Admin\Controller;

class CommentController extends AuthController{

    public function commentViewer(){
        if(empty(I('get.isSearch'))){
            $count = M('comment')->count("comment_id");
//        dump($count);exit;
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('comment','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里
            $list = M('comment')->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            $photo = D('photos');
            $user = D('user');
            $shop = D('shop');
            $j=0;
            for($i=0; $i<count($list); $i++){
                $array_pid = explode(',',$list[$i]['comment_photo_ids']);
                foreach($array_pid as $key=>$value){
                    $photoUrl=$photo->getPhotoUrlByID($value);
                    $p[$j]=C('PIC_URL_PREFIX').$photoUrl;
                    $j++;
                }
                $list[$i]['comment_photo_ids'] = $p;
            }
            for($i=0; $i<count($list); $i++){
                $shop_name=$shop->findShopByShopId($list[$i]['shop_id']);
                $list[$i]['shop_name']=$shop_name['shop_name'];
            }
            for($i=0; $i<count($list); $i++){
                $user_name=$user->findUserByUserId($list[$i]['user_id']);
                $list[$i]['nick_name']=$user_name['nick_name'];
            }
            $this->assign('commentList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->display('commentList');
        }else{
            $user = D('user');
            $shop = D('shop');
            $condition1=I('get.searchUserName');
            $condition2=I('get.searchShopName');
            //查询出用户的id和商店id，然后在评论表中查询记录条数
            $_where['nick_name'] = array('like','%'.$condition1.'%');
            $map['shop_name'] = array('like','%'.$condition2.'%');
            //$count_user = M('user')->where($_where)->field("user_id")->select();
           // $count_shop = M('')->table('user u, comment c')->where('u.id = c.id')->select();;
            print_r('查询功能未完成！');
        //dump($count_user);
            exit;
            $Page = new \Think\Page($count, 10);
            $Page->lastSuffix = false;//最后一页不显示为总页数
            $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>条  <em>%NOW_PAGE%</em>/%TOTAL_PAGE%页</a></li>');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('first','首页');
            $Page->setConfig('comment','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page_show = bootstrap_page_style($Page->show());//重点在这里
            $_where['item_name']=array('like','%'.$condition1.'%');
            $list = M('comment')->where($_where)->limit($Page->firstRow . ',' . $Page->listRows)
                ->order('addTime desc')->select();
            for($i=0; $i<count($list); $i++){
                $photoUrl=D('photos')->getPhotoUrlByID($list[$i]['comment_photo_url']);
                $list[$i]['item_photo_url']=C('PIC_URL_PREFIX').$photoUrl;
            }
//        dump($list);exit;
            $this->assign('commentList', $list);
            if ($count <= 10) {
                $this->assign("page", '<b>共1页</b>');
            } else {
                $this->assign("page", $page_show);
            }
            $this->assign('searchGoodName',$condition);
            $this->display('commentList');
        }
    }

    public function delComment($commentId){
        $commentModel=D('comment');
        $photosModel=D('photos');
        $photoId=$commentModel->getUserPhotoUrl($commentId);
        $commentModel->startTrans();
        $option1=$commentModel->delUserById($commentId);
        $option2=$photosModel->delPhotoById($photoId);
        if($option1 && $option2){
            $commentModel->commit();//成功则提交
            $this->success('删除评论成功！');
        }else{
            $commentModel->rollback();//不成功，则回滚
            $this->error('删除评论失败，数据库连接错误！');
        }
    }
}