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
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Helper;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
	/** \Magento\Catalog\Helper\Image */
	protected $_imageHelper;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Helper\Image $imageHelper
		){
		$this->_imageHelper = $imageHelper;
		parent::__construct($context);
	}

	/**
	 * Get image URL of the given product
	 *
	 * @param \Magento\Catalog\Model\Product	$product		Product
	 * @param int							    $w				Image width
	 * @param int							    $h				Image height
	 * @param string						    $imgVersion		Image version: image, small_image, thumbnail
	 * @param mixed							    $file			Specific file
	 * @return string
	 */
	public function getImg($product, $w=300, $h, $imgVersion='image', $file=NULL)
	{
		if (!$h || (int)$h == 0){
			$image = $this->_imageHelper
			->init($product, $imgVersion)
			->constrainOnly(true)
			->keepAspectRatio(true)
			->keepFrame(false);
			if($file){
				$image->setImageFile($file);
			}
			$image->resize($w);
			return $image;
		}else{
			$image = $this->_imageHelper
			->init($product, $imgVersion);
			if($file){
				$image->setImageFile($file);
			}
			$image->resize($w, $h);
			return $image;
		}
	}

	/**
     * Get alternative image HTML of the given product
     *
     * @param \Magento\Catalog\Model\Product    $product        Product
     * @param int                               $w              Image width
     * @param int                               $h              Image height
     * @param string                            $imgVersion     Image version: image, small_image, thumbnail
     * @return string
     */
	public function getAltImgHtml($product, $w, $h, $imgVersion='small_image', $column = 'position', $value = 1)
	{
		$product->load('media_gallery');
		if ($images = $product->getMediaGalleryImages())
		{
			$image = $images->getItemByColumnValue($column, $value);
			if(isset($image) && $image->getUrl()){
				$imgAlt = $this->getImg($product, $w, $h, $imgVersion , $image->getFile());
				if(!$imgAlt) return '';
				return $imgAlt;
			}else{
				return '';
			}
		}
		return '';
	}
}