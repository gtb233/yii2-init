<?php
/**
 * 后台常用功能
 */

namespace backend\components;

use yii;
use common\components\Helper;
use yii\db\ActiveRecord;
use backend\components\MyActiveRecordBehavior;

class BackendHelper extends Helper
{
    /**
     * 获取服务端ip
     * @param string $dest
     * @param string $port 端口
     * @return string|int
     */
    public static function getMyIp($dest = '64.0.0.0', $port = 80)
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_connect($socket, $dest, $port);
        socket_getsockname($socket, $addr, $port);
        socket_close($socket);
        return $addr;
    }

    /**
     * b转MB，精确到小数点后2位
     * @param $size 文件大小
     * @return string
     */
    public static function fileSizeBKM($size)
    {
        // B/KB/MB单位转换
        if ($size < 1024) {
            $size_BKM = (string)$size . " B";
        } elseif ($size < (1024 * 1024)) {
            $size_BKM = number_format((double)($size / 1024), 2) . " KB";
        } else {
            $size_BKM = number_format((double)($size / (1024 * 1024)), 2) . " MB";
        }
        return $size_BKM;
    }

    /**
     * 为模型添加行为
     * @property yii\db\ActiveRecord | yii\base\Model $model
     * AR类无需额外操作,model类需在成功处添加记录方法
     * $model->modelLogRecord(Model $model, $type, $message = '')
     * @param object $model
     */
    public static function attachAdminLogBehavior($model)
    {
        if ($model instanceof ActiveRecord) {
            yii::$app->session->setFlash($model->tableName() . '_Orig_Attribute', $model->attributes);
        }
        $model->attachBehavior('adminLogRecordBehavior', MyActiveRecordBehavior::className());
    }

    /**
     * 后台侧边栏导航菜单回调处理
     * @param unknown $menu
     * @return [
     *        'label' => $menu['name'],
     *        'url' => [$menu['route']],
     *        'options' => $data,
     *        'items' => $menu['children']
     * ]
     */
    public static function sidebarMenuCallback($menu)
    {
        $data = json_decode($menu['data'], true);
        $items = $menu['children'];
        $return = [
            'label' => $menu['name'],
            'url' => [$menu['route']],
        ];
        //处理我们的配置
        if ($data) {
            //visible
            isset($data['visible']) && $return['visible'] = $data['visible'];
            //icon
            isset($data['icon']) && $data['icon'] && $return['icon'] = $data['icon'];
            //other attribute e.g. class...
            $return['options'] = $data;
        }
        //没配置图标的显示默认图标
        (!isset($return['icon']) || !$return['icon']) && $return['icon'] = 'fa fa-circle-o';
        $items && $return['items'] = $items;

        return $return;
    }
}