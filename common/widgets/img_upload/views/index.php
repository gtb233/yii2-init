<?php
/**
 * 图片上传组件
 */

use yii\helpers\Html;
?>

<div class="img-thumbnail col-md-offset-1">
	<img id="uploadPreview-<?=$inputId?>" class="img-rounded" src="<?=$inputValue?>" style="width: 130px; height: 130px;">
</div>

<?= Html::activeFileInput($model, $attribute, ['accept' => $config['accept'] ])?>