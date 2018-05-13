<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/18
 * Time: 9:42
 */

namespace Home\Model;
use Think\Model\RelationModel;

class ThemeModel extends RelationModel{
    //多对多关联特色店
    protected $_link = array(
        'theme_link_shop'=>array(
            'mapping_type' =>  self::MANY_TO_MANY,
            'class_name'=>'shop',
            //'foreign_key'=>'theme_id',
            'mapping_fields'=>'shop_id, shop_name, desc,location, shop_photo_url',
            //'relation_foreign_key'  =>  'shop_id',
            'relation_table'    =>  'db_theme_shop'

        ),
    );

    /**
     * @param $title
     * @param $name
     * @param $photoUrl
     * @return mixed
     * 添加主题到数据库
     */
    public function doAddTheme($title, $desc, $type, $photoUrl){
        $data['theme_title'] = $title;
        $data['theme_desc'] = $desc;
        $data['theme_type'] = $type;
        $data['theme_photo_url'] = $photoUrl;
        $data['addTime'] = getCurrDateStr();
        return $this->add($data);
    }

    /**
     * @param $themeName
     * @return mixed
     * 通过主题名字返回一条主题记录
     */
    public function findThemeByThemeName($themeName){
        return $this->where(array("theme_name"=>$themeName))->find();
    }

    /**
     * @param $themeTitle
     * @return bool
     * 检查主题是否存在
     */
    public function isThemeExists($themeTitle)
    {
        //查询指定条件的记录个数
        $count = $this->where(array(
            'theme_title' => $themeTitle
        ))->count();

        // 返回
        return 1 == $count;
    }

    /**
     * @param $themeName
     * @return mixed
     * 获取所有的主题
     */
    public function getAllTheme(){
        return $this->select();

    }

    /**
     * @param $pageNo
     * @param $pageSize
     * 分页查询
     */
    public function findThemesInfoByPageNo($pageNo, $pageSize){
        $offset = ($pageNo-1) * $pageSize; //起始行
        return $this->field('theme_id,theme_photo_url,theme_title,theme_type')
            ->order('addTime desc')->limit($offset, $pageSize)->select();
    }

    public function findThemeByName($word){
        $map['theme_title']=array('like','%'.$word.'%');
        $res=$this->where($map)->field('theme_title,theme_id,theme_photo_url,theme_type,theme_desc')->select();
        $i=0;
        foreach ($res as $item) {
            $res[$i]['theme_photo_url']=C('PIC_URL_PREFIX').D('Photos')->getPhotoUrlByID($item['theme_photo_url']);
            ++$i;
        }
//        p($res);exit;
        return $res;
    }
}