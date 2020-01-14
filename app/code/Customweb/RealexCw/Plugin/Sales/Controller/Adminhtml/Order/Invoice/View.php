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

namespace Customweb\RealexCw\Plugin\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Framework\Registry;
class View
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Magento\Backend\Block\Widget\Button\ButtonList
	 */
	protected $_buttonList;

	/**
	 * @var \Magento\Backend\Block\Widget\Button\ToolbarInterface
	 */
	protected $_toolbar;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
	 * @param \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
			\Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		$this->_coreRegistry = $coreRegistry;
		$this->_buttonList = $buttonList;
		$this->_toolbar = $toolbar;
		$this->_transactionFactory = $transactionFactory;
	}

	/**
	 * @param Magento\Sales\Controller\Adminhtml\Order\Invoice\View $subject
	 * @param \Magento\Framework\Controller\ResultInterface $result
	 */
	public function afterExecute(\Magento\Sales\Controller\Adminhtml\Order\Invoice\View $subject, $result)
	{
		if ($this->canCaptureNoClose()) {
			$block = $result->getLayout()->getBlock('sales_invoice_view');
			if ($block->getAuthorization()->isAllowed(
					'Magento_Sales::capture'
			) && $block->getInvoice()->canCapture() && !$this->_isPaymentReview($block)
			) {
				$captureUrl = $block->getUrl('sales/order_invoice/capture', ['invoice_id' => $block->getInvoice()->getId(), 'capture_no_close' => true]);
				$this->_buttonList->add('capture_no_close',
					[
						'label' => __("Capture (Don't Close)"),
						'class' => 'capture',
						'onclick' => 'setLocation(\'' . $captureUrl . '\')'
					], 0, 0, 'toolbar');
				$this->_toolbar->pushButtons($block, $this->_buttonList);
			}
		}
		return $result;
	}

	/**
	 * @return boolean
	 */
	private function canCaptureNoClose()
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
		if ($invoice->getOrder()->getPayment()->isCaptureFinal($invoice->getBaseGrandTotal())) {
			return false;
		}
		return true;
	}

	private function _isPaymentReview(\Magento\Sales\Block\Adminhtml\Order\Invoice\View $block)
	{
		$order = $block->getInvoice()->getOrder();
		return $order->canReviewPayment() || $order->canFetchPaymentReviewUpdate();
	}
}