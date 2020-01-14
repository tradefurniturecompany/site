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
namespace Ves\Megamenu\Block\Adminhtml\Renderer\Fieldset;
use Magento\Theme\Helper\Storage;

class EditorTop extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
\Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_fields;
    
    /**
     * Form element which re-rendering
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $_element;

    /**
     * @var string
     */
    protected $_template = 'editortop.phtml';

    protected $_htmlId;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context  
     * @param \Magento\Framework\Registry             $registry 
     * @param \Ves\Megamenu\Helper\Editor             $editor   
     * @param array                                   $data     
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ves\Megamenu\Helper\Editor $editor,
        array $data = []
        ) {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_fields = $editor->getFields();
    }

    public function _construct(){
        parent::_construct();
        $htmlId = 'ves_megamenu' . time();
        $this->setHmtlId($htmlId);
    }

    public function getHtmlId(){
        return $this->_htmlId;
    }

    public function setHmtlId($htmlId){
        $this->_htmlId = $htmlId;
        return $this;
    }

    public function renderMenuItem($data = [] , $level = 0, $itemBuild = []){
        $_htmlId = $this->getHtmlId();
        $menu = $this->getMenu();
        $menuItems = $menu->getData('menuItems');
        $level++;

        $item = $menuItems[$data['id']];
        $html = $this->_menuItems = json_encode($item) . ',';

        $itemBuild = $item;
        $newChildren = [];
        if(isset($data['children']) && count($data['children']>0)){
            foreach ($data['children'] as $k => $v) {
                $newChildren[] = $this->renderMenuItem($v, $level, $itemBuild);
            }
        }
        $itemBuild['children'] = $newChildren;
        return $itemBuild;
    }

    /**
     * Render element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function getMenu(){
        $model = $this->_coreRegistry->registry('megamenu_menu');
        return $model;
    }

    /**
     * Returns rows array
     * @return array
     */
    public function getFields(){
        return $this->_fields;
    }

    public function getMediaUrl(){
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }
}