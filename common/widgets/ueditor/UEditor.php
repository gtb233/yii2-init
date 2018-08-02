<?php
/**
 * 百度编辑器小工具
 * Date: 2018/1/22
 * Time: 11:10
 */
namespace common\widgets\ueditor;

use yii;
use yii\jui\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

class UEditor extends InputWidget
{
    //配置选项，参阅Ueditor官网文档(定制菜单等)
    public $clientOptions = [];

    //默认配置
    protected $_options;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if (isset($this->options['id'])) {
            $this->id = $this->options['id'];
        } else {
            $this->id = $this->hasModel() ? Html::getInputId($this->model,
                $this->attribute) : $this->id;
        }
        $this->_options = [
            'serverUrl' => Url::to(['ueditor-upload']),
            'initialFrameWidth' => '100%',
            'initialFrameHeight' => '400',
            'lang' => (strtolower(Yii::$app->language) == 'en-us') ? 'en' : 'zh-cn',
        ];
        $this->clientOptions = ArrayHelper::merge($this->_options, $this->clientOptions);
        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        $options = $this->options;
        //$options['maxlength'] = true;
        $options['value'] = $value;

        $contents = [];
        // render a text input
        if ($this->hasModel()) {
            $contents[] = Html::activeTextarea($this->model, $this->attribute, $options);
        } else {
            $contents[] = Html::textarea($this->name, $value, $options);
        }
        return implode("\n", $contents);
    }

    /**
     * 注册客户端脚本
     */
    protected function registerClientScript()
    {
        UEditorAssets::register($this->view);
        $clientOptions = Json::encode($this->clientOptions);
        $script = "UE.getEditor('" . $this->id . "', " . $clientOptions . ");";
        $this->view->registerJs($script, View::POS_READY);
    }

}