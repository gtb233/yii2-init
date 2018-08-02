<?php
/**
 * admin表单数据处理
 */
namespace backend\components;

use backend\models\AdminLog;

class FormHelper
{
    /**
     * 标签所属栏目   1 游戏 2 视频 0共有 顶级不适用
     */
    public static function getTagColumnName($column = 0)
    {
        $tagList  = self::getTagColumnList();
        return $tagList[$column];
    }
    
    /**
     * 标签栏目列表   1 游戏 2 视频 0共有 顶级不适用
     */
    public static function getTagColumnList()
    {
        return array(0=>'公共', 1=>'游戏',2=>'视频');
    }
    
    /**
     * 返回列表图片内容HTML
     * @property $url sting 图片URL地址
     * @property $absoluteUrl bool  是否显示绝对地址
     */
    public static function getImageHtml($url,$absoluteUrl = false)
    {
        if(empty($url)) return '';
        return '<img height="35" src="'.BackendHelper::imageUrlConvert($url, $absoluteUrl).'"/>';
    }
    
    /**
     * 操作日志状态类型名称
     */
    public static function getLogTypeLabel($value=null)
    {
        $array = AdminLog::getActionType();
        if ($value===null ) {
            return '-';
        } else {
            return $array[$value];
        }
    }
    
    /**
     * 返回状态值   0 否（默认） 1是
     */
    public static function getStatusLabelIcon($value=null)
    {
        if ($value===null ) {
            return '-';
        } else {
            return $value ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' ;
        }
    }
    /**
     * 返回状态值   0 否（默认） 1是
     */
    public static function getStatusLabelOptions()
    {
        return array(''=> '全部', '0'=>'禁用','1'=>'启用');
    }
}