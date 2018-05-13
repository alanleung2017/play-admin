<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/20
 * Time: 15:16
 */

namespace Home\Model;
use Think\Model;

class CommentModel extends Model
{
    //后台数据校验
    protected $_validate = array(
        array('comment_content','require','评论内容不能为空！') //默认情况下用正则进行验证
    );

    /**
     * @param $postData
     * @return mixed
     * 添加评论到数据库
     */
    /*public function doAddComment($postData){
        $data['comment_content'] = $postData['comment_content'];
        $data['user_id'] = $postData['comment_user_id'];
        $data['shop_id'] = $postData['comment_shop_id'];
        $data['comment_photo_ids'] = $postData['comment_photo_ids'];
        $data['addTime'] = getCurrDateStr();
        return $this->add($data);
    }*/

    public function getShopCommentByShopId($shopid, $pageNo, $pageSize){
        $offset = ($pageNo-1)*$pageSize; //起始行
        $res=$this->where(array('shop_id'=>$shopid))->field('user_id,comment_content,comment_photo_ids,addTime')->order('addTime desc')->limit($offset, $pageSize)->select();
        $i=0;
        foreach ($res as $r) {
            $user=D('User')->findUserByUserId($r['user_id']);
            $res[$i]['username']=$user['user_account'];
            $res[$i]['user_photo']=C('PIC_URL_PREFIX').D('Photos')->getPhotoUrlByID($user['user_photo_url']);
            $res[$i]['commemt']['comment_content']=$r['comment_content'];
            $photo_url_ids=explode(',',$r['comment_photo_ids']);
            $photo_url_arr=array();
            foreach ($photo_url_ids as $pid) {
                $oneUrl=C('PIC_URL_PREFIX').D('Photos')->getPhotoUrlByID($pid);
                array_push($photo_url_arr, $oneUrl);
            }
            $res[$i]['commemt']['comment_photos']=$photo_url_arr;
            unset($res[$i]['comment_content']);
            unset($res[$i]['comment_photo_ids']);
            ++$i;
        }
//        p($res);exit;
        return $res;
    }

    public function doAddComment($shop_id, $user_id, $comment_content, $comment_photo_ids){
        $data = array(
            'comment_content'=>$comment_content,
            'user_id'=>$user_id,
            'shop_id'=>$shop_id,
            'comment_photo_ids'=>$comment_photo_ids,
            'addTime'=>getCurrDateStr()
        );

        return $this->add($data);
    }
}