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
namespace Ves\Megamenu\Model;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Menu extends \Magento\Framework\Model\AbstractModel
{
	const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Menu cache tag
     */
    const CACHE_TAG = 'megamenu_menu';
    const CACHE_WIDGET_TAG = 'megamenu_menu_widget';
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_blogHelper;

    protected $_resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $_store;

    protected $_logged_customer_group_id;

    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Ves\Blog\Model\ResourceModel\Blog|null                      $resource                 
     * @param \Ves\Blog\Model\ResourceModel\Blog\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Ves\Blog\Helper\Data                                    $brandHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Megamenu\Model\ResourceModel\Menu $resource = null,
        \Ves\Megamenu\Model\ResourceModel\Menu\Collection $resourceCollection = null,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ves\Megamenu\Model\ResourceModel\Menu');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getMobileTemplates()
    {
        return [
            0 => __('Off Canvas Left'),
            1 => __('Accordion Menu'),
            2 => __('Custom Menu Alias'),
            3 => __('Drill Down Menu')
        ];
    }

    public function getDesktopTemplates()
    {
        return [
            'vertical-left'   => __('Vertical Menu Left'),
            'vertical-right'   => __('Vertical Menu Right'),
            'horizontal' => __('Horizontal Menu')
        ];
    }

    public function getEventType()
    {
        return [
            'hover' => __('Hover'),
            'click' => __('Click')
        ];
    }

    /**
     * Set store model
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if($this->_store){
            return $this->_storeManager->getStore();
        }else{
            return false;
        }
    }

    public function setLoggedCustomerGroupId($customer_group_id = 0)
    {
        $this->_logged_customer_group_id = $customer_group_id;
        return $this;
    }

    public function getLoggedCustomerGroupId()
    {
        if($this->_logged_customer_group_id){
            return $this->_logged_customer_group_id;
        }else{
            return 0;
        }
    }

    /**
     * Load object data
     *
     * @param integer $modelId
     * @param null|string $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        $this->_beforeLoad($modelId, $field);
        $store = $this->getStore();
        $customer_group_id = $this->getLoggedCustomerGroupId();
        $this->_getResource()->setStore($store)
                            ->setLoggedCustomerGroupId($customer_group_id)
                            ->load($this, $modelId, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        $this->updateStoredData();
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getAlias()];
    }

    /**
     * Synchronize object's stored data with the actual data
     *
     * @return $this
     */
    private function updateStoredData()
    {
        if (isset($this->_data)) {
            $this->storedData = $this->_data;
        } else {
            $this->storedData = [];
        }
        return $this;
    }
    public function getStoresIds(){
        return $this->_getResource()->lookupStoreIds($this->getId());
    }
}