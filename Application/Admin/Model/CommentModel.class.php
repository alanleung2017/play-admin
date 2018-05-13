<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/18
 * Time: 9:42
 */

namespace Admin\Model;
use Think\Model\RelationModel;

class CommentModel extends RelationModel{

    public function doDelComment($commentId){
        return $this->where(array("comment_id"=>$commentId))->delete();
    }




}