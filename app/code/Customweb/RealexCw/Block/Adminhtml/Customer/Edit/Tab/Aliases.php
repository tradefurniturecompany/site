<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Block\Adminhtml\Customer\Edit\Tab;

class Aliases extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Customweb\RealexCw\Model\ResourceModel\Grid\PaymentMethodList
     */
    protected $_paymentMethodGridList;

    /**
     * @var \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Customweb\RealexCw\Model\ResourceModel\Grid\PaymentMethodList $paymentMethodGridList
     * @param \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
    		\Magento\Backend\Block\Template\Context $context,
    		\Magento\Backend\Helper\Data $backendHelper,
    		\Magento\Framework\Registry $coreRegistry,
    		\Customweb\RealexCw\Model\ResourceModel\Grid\PaymentMethodList $paymentMethodGridList,
    		\Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $collectionFactory,
    		array $data = []
    ) {
    	parent::__construct($context, $backendHelper, $data);
    	$this->_coreRegistry = $coreRegistry;
    	$this->_paymentMethodGridList = $paymentMethodGridList;
    	$this->_collectionFactory = $collectionFactory;
    }

    protected function _construct()
    {
    	parent::_construct();
    	$this->setId('realexcw_customer_alias_grid');
    	$this->setDefaultSort('created_on', 'desc');
    	$this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
    	$collection = $this->_collectionFactory->create()->addFieldToSelect(
    		'entity_id'
    	)->addFieldToSelect(
            'increment_id'
    	)->addFieldToSelect(
    		'alias_for_display'
    	)->addFieldToSelect(
    		'alias_active'
    	)->addFieldToSelect(
    		'payment_method'
    	)->addFieldToSelect(
    		'customer_id'
    	)->addFieldToSelect(
    		'created_at'
    	)->addFieldToSelect(
    		'store_id'
    	)->addFieldToFilter(
			'alias_active',
    		1
    	)->addFieldToFilter(
    		'alias_for_display',
    		['notnull' => true]
    	)->addFieldToFilter(
    		'customer_id',
			$this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID)
    	);

    	$this->setCollection($collection);
    	return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
    	$this->addColumn(
    			'alias_for_display', [
    				'header' => __('Alias'),
    				'width' => '100',
    				'index' => 'alias_for_display',
					'header_css_class' => 'col-alias-for-display',
    				'column_css_class' => 'col-alias-for-display'
    			]
    	);

    	$this->addColumn(
    			'created_at',
    			[
    				'header' => __('Creation Date'),
    				'index' => 'created_at',
    				'type' => 'datetime',
    				'header_css_class' => 'col-period',
    				'column_css_class' => 'col-period'
    			]
    	);

    	$this->addColumn(
    			'payment_method',
    			[
    				'header' => __('Payment Method'),
    				'index' => 'payment_method',
    				'type' => 'options',
    				'options' => $this->_paymentMethodGridList->toOptionArray(),
    				'header_css_class' => 'col-method',
    				'column_css_class' => 'col-method'
    			]
    	);

    	if (!$this->_storeManager->isSingleStoreMode()) {
    		$this->addColumn(
    				'store_id',
    				[
    					'header' => __('Purchase Point'),
    					'index' => 'store_id',
    					'type' => 'store',
    					'store_view' => true,
    					'header_css_class' => 'col-from-store',
    					'column_css_class' => 'col-from-store'
    				]
    		);
    	}

    	$this->addColumn(
    			'action',
    			[
    				'header' => __('Action'),
    				'type' => 'action',
    				'getter' => 'getId',
    				'actions' => [
    					[
    						'caption' => __('Delete'),
    						'url' => [
    							'base' => '*/*/deleteAlias'
    						],
    						'field' => 'id'
    					]
    				],
    				'filter' => false,
    				'sortable' => false,
    				'index' => 'stores',
    				'header_css_class' => 'col-action',
    				'column_css_class' => 'col-action'
    			]
    	);

    	return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
    	return $this->getUrl('realexcw/customer/aliases', ['_current' => true]);
    }



    public function getTabLabel()
    {
    	return __('Realex Aliases');
    }

    public function getTabTitle()
    {
    	return __('Realex Aliases');
    }

    public function getTabClass()
    {
    	return '';
    }

    public function getTabUrl()
    {
    	return $this->getUrl('realexcw/customer/aliases', ['_current' => true]);
    }

    public function isAjaxLoaded()
    {
    	return true;
    }

    public function canShowTab()
    {
    	return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function isHidden()
    {
    	return false;
    }
}
