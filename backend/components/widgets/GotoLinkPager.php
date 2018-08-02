<?php
/**
 * 分页扩展类(针对分页列表加上跳转框
 */
namespace backend\components\widgets;

use yii;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class GotoLinkPager extends LinkPager
{
    #public $firstPageLabel = true; //可bool|string bool时返回页码 
    #public $lastPageLabel = '尾页';
    #public $nextPageLabel = '下一页';
    #public $prevPageLabel = '上一页';
    #public $maxButtonCount = 5;
    #public $options = ['class' => 'm-pagination'];
    #public $registerLinkTags = true;
    
    /**
     *@var boole|string. 显示总页数
     *默认不使用
     * eg: totalPageLable => '共x页'. 注意必须有 'x' 用于替换总页数  
     */
    public $totalPageLable = false;
    /**
     *@var 是否显示输入框 ，默认不显示。与按钮共用
     */
    public $goPageLabel = false;
    /**
     *@var array.options about the goPageLabel(input)
     *goPageLabelOptions => [
     *		'class' =>
     *		'data-num' =>
     *		'style' =>
     *	]
     */
    public $goPageLabelOptions = [];
    /**
	*@var boole | string. 按钮
	* ```php
    * 'goButtonLable' => 'GO'
	*/
	public $goButtonLable = false;

	/**
	*@var array.options about the goButton
	*/
	public $goButtonLableOptions = [];
    
    
    public function init()
    {
        parent::init();
    }
    
    public function run()
    {
        parent::run();
    }
    
    /**
     * Renders the page buttons.
     * @return string the rendering result
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }
    
        $buttons = [];
        $currentPage = $this->pagination->getPage(); #当前页数据提供
    
        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
        }
    
        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }
    
        // internal pages
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i == $currentPage);
        }
    
        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }
    
        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }
    
        // 总页数显示框
        $totalPageLable = $this->totalPageLable === true ? $pageCount : $this->totalPageLable;
        if ($totalPageLable !== false) {
            $buttons[] = $this->renderPageButton(str_replace('x',$pageCount,$this->totalPageLable), $pageCount - 1, $this->lastPageCssClass, true , false);
            //$buttons[] = Html::tag('li',Html::a(str_replace('x',$pageCount,$this->totalPageLable),'javascript:return false;',[]),[]);
        }
        
        //gopage 输入框
        if ($this->goPageLabel) {
            $input = Html::input('number',$this->pagination->pageParam,$currentPage+1,array_merge([
                'min' => 1,
                'max' => $pageCount,
                'style' => 'height:34px;width:80px;display:inline-block;margin:0 3px 0 3px',
                'class' => 'form-control',
            ],$this->goPageLabelOptions));
        
            $buttons[] = Html::tag('li',$input,[]);
        }
        
        // gobuttonlink 按钮
        if ($this->goPageLabel) {
            $buttons[] = Html::submitInput($this->goButtonLable ? $this->goButtonLable : '跳转',array_merge([
                'style' => 'height:34px;display:inline-block;',
                'class' => 'btn btn-primary'
            ],$this->goButtonLableOptions));
        }
        
        $ul = Html::tag('ul', implode("\n", $buttons), $this->options);
        return Html::tag('form',$ul,[]);
    }
    
    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = ['class' => empty($class) ? $this->pageCssClass : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $tag = ArrayHelper::remove($this->disabledListItemSubTagOptions, 'tag', 'span');
    
            return Html::tag('li', Html::tag($tag, $label, $this->disabledListItemSubTagOptions), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;
    
        return Html::tag('li', Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
    }
}