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

namespace Ves\Megamenu\Block;

class MobileMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ves\Megamenu\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ves\Megamenu\Model\Menu
     */
    protected $_menu;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ves\Megamenu\Helper\Data $helper,
        \Ves\Megamenu\Model\Menu $menu,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
        ) {
        parent::__construct($context, $data);
        $this->_helper          = $helper;
        $this->_menu            = $menu;
        $this->_objectManager   = $objectManager;
        $this->_customerSession = $customerSession;
    }
    public function getCustomerGroupId(){
        if(!isset($this->_customer_group_id)) {
            $this->_customer_group_id = (int)$this->_customerSession->getCustomerGroupId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $context = $objectManager->get('Magento\Framework\App\Http\Context');
            $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
            if(!$isLoggedIn) {
               $this->_customer_group_id = 0;
            }
        }
        return $this->_customer_group_id;
        
    }
    public function _toHtml()
    {
        if(!$this->getTemplate()){
            $this->setTemplate("Ves_Megamenu::mobile_menu.phtml");
        }
        $store = $this->_storeManager->getStore();
        $html = $menu = '';
        $menu = $this->getData('menu');
        $alias = $this->getData('alias');
        $menu_id = $this->getData('id');

        if(!$menu) {
            $menu = $this->getMenuProfile($menu_id, $alias);
        }

        if($menu){
            $customerGroups = $menu->getData('customer_group_ids');
            $customerGroupId = (int)$this->getCustomerGroupId();
            if($customerGroupId){
                if(!in_array($customerGroupId, $customerGroups)) return;
            }
            $this->setData("menu", $menu);
        }
        
        return parent::_toHtml();
    }
    public function getMenuProfile($menuId = 0, $alias = ""){
        $menu = false;
        $store = $this->_storeManager->getStore();
        if($menuId){
            $menu = $this->_menu->setStore($store)->load((int)$menuId);
            if ($menu->getId() != $menuId) {
                $menu = false;
            }
        } elseif($alias){
            $menu = $this->_menu->setStore($store)->load(addslashes($alias));
            if ($menu->getAlias() != $alias) {
                $menu = false;
            }
        }
        if ($menu && !$menu->getStatus()) {
            $menu = false;
        }
        return $menu;
    }
    public function getConfig($key, $default = NULL){
        if($this->hasData($key)){
            return $this->getData($key);
        }
        return $default;
    }
}
