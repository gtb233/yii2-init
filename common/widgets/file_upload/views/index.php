<?php
/**
 * 图片上传组件
 */

use yii\helpers\Html;
use common\components\Helper;
?>
<div class="per_upload_con" data-url="<?=$config['serverUrl']?>">
    <div class="per_real_img <?=$attribute?>" domain-url = "<?=$config['domain_url']?>">
        <?=isset($inputValue)?'<img src="'.Helper::imageUrlConvert($inputValue,$config['isCdn']).'">':''?>
    </div>
    <div class="per_upload_img">图片上传</div>
    <div class="per_upload_text">
        <p class="upbtn" ><a id="<?=$attribute?>" href="javascript:;" class="btn btn-success green choose_btn">选择图片</a></p>
        <p class="rule">仅支持文件格式为jpg、jpeg、png以及gif<br>大小在<?=$config['maxSize'];?>以下的文件</p>
    </div>
    <input up-id="<?=$attribute?>" type="hidden" name="<?=$inputName?>" upname='<?=$config['fileName']?>' value="<?=isset($inputValue)?$inputValue:''?>" filetype="img" />
</div>