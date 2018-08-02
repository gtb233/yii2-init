<?php
/**
 * 后台日志记录
 * AR类无需额外操作
 * Model 类需在成功处添加记录方法  modelLogRecord
 */
namespace backend\components;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\Model;
use backend\models\AdminLog;
use yii\base\Object;

class MyActiveRecordBehavior extends Behavior
{
    private $logModel = null;
    public function __construct($config = [])
    {
        $this->logModel = new AdminLog();
        parent::__construct($config);
    }
    
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'activeRecordLog',
            ActiveRecord::EVENT_AFTER_UPDATE => 'activeRecordLog',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteRecordLog',
        ];
    }

    public function activeRecordLog($event)
    {
        $model = $event->sender;
        if($model instanceof ActiveRecord){
            if ( !$model->getIsNewRecord() ) {
                if ( Yii::$app->session->hasFlash($model->tableName(). '_Orig_Attribute') ) {
                    $origData= Yii::$app->session->getFlash($model->tableName(). '_Orig_Attribute');
                    $newData= $model->attributes;
                    $actionInfo = '更新数据ID#'. $model->getPrimaryKey().',';
                    $insert = $modify = '';
                    foreach ($newData as $k=> $v) {
                        if(in_array($k,array('created_at','updated_at','create_user_id','update_user_id'))){
                            continue;
                        }
                        if ( !isset($origData[$k]) && $origData[$k]!==null) {
            
                            $insert .= "{$model->getAttributeLabel($k)},";
                        } elseif ($newData[$k]!= $origData[$k]) {
            
                            $modify .= "{$model->getAttributeLabel($k)},";
                        }
                    }
                    $actionInfo .= $insert? '补充:"'.$insert.'"' : '';
                    $actionInfo .= $modify? '修改:"'.$modify.'"' : '';
                    //print_r($origData);print_r($newData);echo $actionInfo;die;
                } else {
                    $actionInfo = '更新数据ID#'. $model->getPrimaryKey();
                }
                $this->logModel->actionRecord(AdminLog::ACTION_TYPE_UPDATE, array(
                    'action_info'=> $actionInfo,
                    'action_model'=> $model::className(),
                ) );
            } else {
                $actionInfo = '新增数据ID#'. $model->getPrimaryKey();
                $this->logModel->actionRecord(AdminLog::ACTION_TYPE_CREATE, array(
                    'action_info'=> $actionInfo,
                    'action_model'=> $model::className(),
                ) );
            }
        }
    }
    
    public function deleteRecordLog($event)
    {
        $model = $event->sender;
        $actionInfo = '删除数据ID#'. $model->getPrimaryKey();
        $this->logModel->actionRecord(AdminLog::ACTION_TYPE_DELETE, array(
            'action_info'=> $actionInfo,
            'action_model'=> $model::className(),
        ) );
    }
    
    /**
     * 添加非AR类操作记录日志
     * @param object $model 继承自\yii\base\model
     * @param int $type 事件类型
     * @param string $message 特殊操作，手动记录信息
     */
    public function modelLogRecord(Model $model, $type, $message = '')
    {
        if (AdminLog::ACTION_TYPE_LOGIN == $type){
            $actionInfo = '用户登录#' . (isset($model->username) ? $model->username : '');
        }elseif (AdminLog::ACTION_TYPE_SIGNUP == $type){
            $actionInfo = '用户注册#' . (isset($model->username) ? $model->username : '');
        }elseif (AdminLog::ACTION_TYPE_AUTH == $type){
            $actionInfo = '权限操作';
        }else{
            $actionInfo = '#';
        }
        $actionInfo = $actionInfo .'#'. $message;
        
        $this->logModel->actionRecord($type, array(
            'action_info'=> $actionInfo,
            'action_model'=> $model::className(),
        ) );
    }
}