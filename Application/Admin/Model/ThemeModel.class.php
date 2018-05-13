<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/10/18
 * Time: 9:42
 */

namespace Admin\Model;
use Think\Model\RelationModel;

class ThemeModel extends RelationModel{
    //多对多关联特色店
    protected $_link = array(
        'theme_link_shop'=>array(
            'mapping_type' =>  self::MANY_TO_MANY,
            'class_name'=>'shop',
            'foreign_key'=>'theme_id',
            'mapping_fields'=>'shop_id, shop_name, desc,location, shop_photo_url',
            'relation_foreign_key'  =>  'shop_id',
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
    public function doAddTheme($title, $desc, $type, $photoId){
        $data['theme_title'] = $title;
        $data['theme_desc'] = $desc;
        $data['theme_type'] = $type;
        $data['theme_photo_url'] = $photoId;
        $data['addTime'] = getCurrDateStr();
        return $this->add($data);
    }

    public function doEditTheme($theme_id, $title, $desc, $type, $photoUrl){
        $data['theme_title'] = $title;
        $data['theme_desc'] = $desc;
        $data['theme_type'] = $type;
        $data['theme_photo_url'] = $photoUrl;
        $data['addTime'] = getCurrDateStr();
        return $this->where(array("theme_id"=>$theme_id))->save($data);
    }

    public function doDelTheme($themeId){
        return $this->where(array("theme_id"=>$themeId))->delete();
    }

    public function getThemePhotoUrl($themeId){
        return $this->where(array("theme_id"=>$themeId))->getField('theme_photo_url');
    }
    /**
     * @param $themeName
     * @return mixed
     * 通过主题名字返回一条主题记录
     */
    public function findThemeByThemeName($themeName){
        return $this->where(array("theme_name"=>$themeName))->find();
    }

    public function findThemeByThemeId($themeId){
        return $this->where(array("theme_id"=>$themeId))->find();
    }

    public function getThemeNameByThemeId($themeId){
        return $this->where(array("theme_id"=>$themeId))->getField('theme_title');
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

    public function getThemeNameById($themeId){
        return $this->where(array("theme_id"=>$themeId))->getField('theme_title');
    }

}