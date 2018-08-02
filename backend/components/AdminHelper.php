<?php
/**
 * admin表单数据处理
 */
namespace backend\components;

use backend\models\AdminLog;

class AdminHelper
{
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
    
}