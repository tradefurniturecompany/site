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
namespace Ves\Megamenu\Model\Config\Source;

class CategoryList implements \Magento\Framework\Option\ArrayInterface
{
    static $arr = array();
    static $tmp = array();

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper = null;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Cms\Model\Block $blockModel
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
        ) {
        $this->_escaper = $escaper;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray($showEmpty = true)
    {
        $arr = [];
        $collection = $this->_categoryFactory->create();
        foreach ($this->_storeManager->getGroups() as $store) {
            $categories = $this->getTreeCategories($store->getRootCategoryId());
            if(!empty($categories)){
                $rootCat = $this->_categoryFactory->create()->load($store->getRootCategoryId());
                $arr[] = [
                    'label' => $rootCat->getName(),
                    'value' => $categories
                ];
            }
        }

        if($showEmpty){
            array_unshift($arr, array(
                'value' => '',
                'label' => ' ',
                ));
        }
        return $arr;
    }

    public function getTreeCategories($parentId,$level = 0, $caret = ' _ '){
        $allCats = $this->_categoryFactory->create()->getCollection()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('is_active','1')
        ->addAttributeToSort('position', 'asc'); 
        if ($parentId) {
            $allCats->addAttributeToFilter('parent_id',array('eq' => $parentId));
        }

        $prefix = "";
        if($level) {
            $prefix = "|_";
            for($i=0;$i < $level; $i++) {
                $prefix .= $caret;
            }
        }
        foreach($allCats as $category)
        {
            if(!isset(self::$tmp[$category->getId()])) {
                self::$tmp[$category->getId()] = $category->getId();
                $tmp["value"] = $category->getId();
                $tmp["label"] = $prefix."(ID:".$category->getId().") ".addslashes($category->getName());
                $arr[] = $tmp;
                $subcats = $category->getChildren();
                if($subcats != ''){ 
                    $arr = array_merge($arr, $this->getTreeCategories($category->getId(),(int)$level + 1, $caret.' _ '));
                }

            }
            
        }
        return isset($arr)?$arr:array();
    }
}