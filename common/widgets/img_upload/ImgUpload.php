<?php
namespace common\widgets\img_upload;

/**
 * 图片上传 视图代码生成部件
 */
use Yii;
use yii\widgets\InputWidget;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\components\Helper;

class ImgUpload extends InputWidget
{
    public $config = [];
    
    public $value = '';
    
    public function init()
    {
        $_config = [
            'isCdn' => true,  //默认启用
            'accept' => 'image/*',
        ];
        $this->config = ArrayHelper::merge($_config, $this->config);
    }
    
    public function run()
    {
        if ($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
            $inputValue = Html::getAttributeValue($this->model, $this->attribute);
            $previewName = 'uploadPreview-'.$inputId;
            $this->registerClientScript($inputId, $previewName);
            
            return $this->render('index',[
                'config'=>$this->config,
                'inputId' => $inputId,
                'inputValue' => $this->config['isCdn'] ? Helper::imageUrlConvert($inputValue) : $inputValue,
                'attribute' => $this->attribute,
                'model' => $this->model,
            ]);
        } else {
//             return $this->render('index',[
//                 'config'=>$this->config,
//                 'inputName' => 'file-upload',
//                 'inputValue'=> $this->value
//             ]);
        }
    }
    
    protected function registerClientScript($inputId, $previewName)
    {
        $script = <<<EOF
    $("#{$inputId}").on("change", function(){
	    // Get a reference to the fileList
	    var files = !!this.files ? this.files : [];
	 
	    // If no files were selected, or no FileReader support, return
	    if (!files.length || !window.FileReader) return;
	 
	    // 正则判断是否是图片类型
	    if (/^image/.test( files[0].type)){
	        // Create a new instance of the FileReader
	        var reader = new FileReader();
	        // 读取本地文件到DataURL，结果存储在result属性 
	        reader.readAsDataURL(files[0]);

	        //When loaded, set image data as background of div
	        reader.onloadend = function(){
				$("#{$previewName}").attr({src:this.result, title: "预览文件"});     
	        };
	    }	 
	});
EOF;
        $this->view->registerJs($script, View::POS_READY);
    }
}