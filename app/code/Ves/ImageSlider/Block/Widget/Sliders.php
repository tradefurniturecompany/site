<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_ImageSlider
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\ImageSlider\Block\Widget;

class Sliders extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_blockModel;
	protected $_dataHelper;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Cms\Model\Block $blockModel,
		\Ves\ImageSlider\Helper\Data $dataHelper,
		array $data = []
		) {
		parent::__construct($context, $data);
		$this->_blockModel = $blockModel;
		$this->_dataHelper = $dataHelper;
	}

	public function _toHtml(){
		$this->setTemplate("widget/slides.phtml");
		return parent::_toHtml();
	}
	public function getConfig($key, $default = NULL){
		if($this->hasData($key)){
			return $this->getData($key);
		}
		return $default;
	}

	public function getSlides(){
		$data = $this->getData();
		$slides = [];
		foreach ($data as $k => $v) {
			if(preg_match("/block_/", $k)){
				$html = '';
				$number = str_replace("block_", "", $k);
				if(is_numeric($v))	{
					$block = $this->_blockModel->load($v);
					$html = $block->getContent();
				}elseif(isset($data["html_".$number]) && $data["html_".$number]!=''){
					$html = $data["html_".$number];
					if(base64_decode($html, true) == true){
						$html = str_replace(" ", "+", $html);
						$html = base64_decode($html);
					}
				}
				if($html!=''){
					$html = $this->_dataHelper->decodeImg($html);
					$html = $this->_dataHelper->filter($html);
					$slides[] = $html;
				}
			}
		} 
		return $slides;
	}

	/**
	 * @return array
	 */
	public function getBlocks(){
		return [];
		$blocks = $this->getConfig('blocks');
		$result = [];
		if($blocks){
			$blocks = explode(',', $blocks);
			$collection = $this->_blockModel->getCollection()
			->addFieldToFilter('identifier',['in'=>$blocks]);
			foreach ($blocks as $k => $v) {
				foreach ($collection as $_block) {
					if($v == $_block->getIdentifier()){
						$result[] = $_block;
					}
				}
			}
		}
		return $result;
	}
}