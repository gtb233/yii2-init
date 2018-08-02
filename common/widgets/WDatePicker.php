<?php
/**
 * 日期选择器小部件 My97DatePicker
 * User: gtb
 * Date: 2017/12/26
 * Time: 14:04
 */
namespace common\widgets;

use common\assets\DatePickerAsset;
use yii;
use yii\jui\InputWidget;
use yii\base\InvalidParamException;
use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\jui\JuiAsset;

class WDatePicker extends InputWidget
{
    /**
     * @var string 语言(e.g. 'fr', 'de', 'en-GB', 'zh-TW', 'zh-CN')
     * 默认自动根据客户端浏览器的语言自动选择语言
     */
    public $language = 'auto';

    public $inline = false;

    /**
     * @var array 标签属性 如： ['id' => 'username']
     */
    public $containerOptions = [];

    /**
     * @var 显示格式
     *
     * For example:
     *
     * ```php
     * 'yyyy-MM-dd' // 不显示时间选项
     * 'yyyy-MM-dd HH:mm:ss' // 显示时间选项
     * ```
     */
    public $dateFormat;

    /**
     * @var string the model attribute that this widget is associated with.
     * The value of the attribute will be converted using [[\yii\i18n\Formatter::asDate()|`Yii::$app->formatter->asDate()`]]
     * with the [[dateFormat]] if it is not null.
     */
    public $attribute;

    /* 是否只读(手动输入) */
    public $readOnly = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->inline && !isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = $this->options['id'] . '-container';
        }
        if ($this->dateFormat === null) {
            $this->dateFormat = Yii::$app->formatter->dateFormat;
        }

        if (isset($this->readOnly) && $this->readOnly) {
            $this->clientOptions['readOnly'] = $this->readOnly;
        }

    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        $language = $this->language ? $this->language : Yii::$app->language;

        $this->clientOptions['dateFmt'] = $this->dateFormat;
        $this->clientOptions['el'] = $containerID;
        $this->clientOptions['lang'] = Html::encode($language);

        $view = $this->getView();

        DatePickerAsset::register($view);

        $options = Json::htmlEncode($this->clientOptions);

        $view->registerJs("jQuery('#{$containerID}').on('click', function(){ WdatePicker({$options}) })");
    }

    /**
     * Renders the DatePicker widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                $value = Yii::$app->formatter->asDate($value, $this->dateFormat);
            } catch(InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        $options = $this->options;
        $options['value'] = $value;

        if ($this->inline === false) {
            // render a text input
            if ($this->hasModel()) {
                $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::textInput($this->name, $value, $options);
            }
        } else {
            // render an inline date picker with hidden input
            if ($this->hasModel()) {
                $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::hiddenInput($this->name, $value, $options);
            }
            $this->clientOptions['defaultDate'] = $value;
            $this->clientOptions['altField'] = '#' . $this->options['id'];
            $contents[] = Html::tag('div', null, $this->containerOptions);
        }

        return implode("\n", $contents);
    }
}