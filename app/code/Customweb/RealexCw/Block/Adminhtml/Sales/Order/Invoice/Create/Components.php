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

namespace Customweb\RealexCw\Block\Adminhtml\Sales\Order\Invoice\Create;

class Components extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var string
	 */
	protected $_template = 'Customweb_RealexCw::sales/order/invoice/create/components.phtml';

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private $transaction;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\Registry $registry,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			array $data = []
	) {
		$this->_coreRegistry = $registry;
		$this->_transactionFactory = $transactionFactory;
		parent::__construct($context, $data);
	}

	/**
	 * @return boolean
	 */
	public function canCaptureNoClose()
	{
		$invoice = $this->_coreRegistry->registry('current_invoice');
		if (!($invoice instanceof \Magento\Sales\Model\Order\Invoice)) {
			return false;
		}
		if (!($invoice->getOrder()->getPayment()->getMethodInstance() instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod)) {
			return false;
		}
		$transaction = $this->_transactionFactory->create()->loadByOrderId($invoice->getOrderId());
		if (!$transaction->getId()) {
			return false;
		}
		if (!$transaction->getTransactionObject()->isPartialCapturePossible()) {
			return false;
		}
		return true;
	}
}