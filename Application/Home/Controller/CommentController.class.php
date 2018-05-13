<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2017/5/2
 * Time: 12:29
 */

namespace Home\Controller;
use Think\Controller;
use Common\Common\Utils\Response;

class CommentController extends Controller{
    public function ShopComment($shopid, $from=1, $pageSize=20){
        $res=D('Comment')->getShopCommentByShopId($shopid, $from, $pageSize);
        if(!empty($res)){
            return Response::show(200, '商店评论获取成功', $res);
        } else {
            return Response::show(400, '商店评论获取失败，请检查URL参数', $res);
        }
    }

    public function addComment(){
        //参数检查
        $shop_id=I('post.shopid');
        $user_id=I('post.userid');
        $comment_content=I('post.comment');
        if(empty($shop_id) || empty($user_id) || empty($comment_content)){
            return Response::show(400, '添加评论失败：请检查post参数是否为空', array());
        } else{
            //添加评论
            $r=D('Comment')->doAddComment($shop_id, $user_id, $comment_content/*, $comment_photo_ids*/);
            if($r !== false){
                $data = array('result'=>'true');
                return Response::show(200, '添加评论成功', $data);
            } else {
                return Response::show(500, '添加评论失败：数据库连接错误', array());
            }
        }
    }
}