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

namespace Customweb\RealexCw\Block\Customer;

class Aliases extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'customer/aliases.phtml';

    /**
     * @var \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\Collection
     */
    protected $aliases;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
    	\Magento\Payment\Helper\Data $paymentHelper,
    	\Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $collectionFactory,
        array $data = []
    ) {
    	parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->_paymentHelper = $paymentHelper;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Realex Aliases'));
    }

    /**
     * @return bool|\Customweb\RealexCw\ResourceModel\Authorization\Transaction\Collection
     */
    public function getAliases()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->aliases) {
            $this->aliases = $this->_collectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
				'alias_active',
    			1
    		)->addFieldToFilter(
    			'alias_for_display',
    			['notnull' => true]
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->aliases;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'realexcw.customer.alias.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param \Customweb\RealexCw\Model\Authorization\Transaction $transaction
     * @return string
     */
    public function getDeleteUrl(\Customweb\RealexCw\Model\Authorization\Transaction $transaction)
    {
        return $this->getUrl('realexcw/customer/deleteAlias', ['id' => $transaction->getId()]);
    }

    /**
     * @param string $code
     * @return string
     */
    public function formatPaymentMethod($code)
    {
		return $this->_paymentHelper->getMethodInstance($code)->getTitle();
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
