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

namespace Customweb\RealexCw\Plugin\Sales\Block\Adminhtml\Order;

class View
{
	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 */
	public function __construct(
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Magento\Framework\Registry $coreRegistry
	) {
		$this->_transactionFactory = $transactionFactory;
		$this->_coreRegistry = $coreRegistry;
	}

	public function aroundGetReviewPaymentUrl(\Magento\Sales\Block\Adminhtml\Order\View $subject, \Closure $proceed, $action)
    {
    	if ($subject->getOrder() instanceof \Magento\Sales\Model\Order
    			&& $subject->getOrder()->getPayment() instanceof \Magento\Sales\Model\Order\Payment
	    		&& $subject->getOrder()->getPayment()->getMethodInstance() instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
	    	$transaction = $this->_transactionFactory->create()->loadByOrderId($subject->getOrderId());
	    	if ($transaction->getId()) {
	    		return $subject->getUrl('realexcw/transaction/reviewPayment', ['action' => $action, 'transaction_id' => $transaction->getId()]);
	    	}
    	}
    	return $proceed($action);
    }

    public function beforeGetOrder(\Magento\Sales\Block\Adminhtml\Order\View $subject)
    {
		$this->_coreRegistry->register('realexcw_order_view', true, true);
    }
}