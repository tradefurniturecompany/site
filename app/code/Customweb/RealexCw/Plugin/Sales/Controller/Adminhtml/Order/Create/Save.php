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

namespace Customweb\RealexCw\Plugin\Sales\Controller\Adminhtml\Order\Create;

class Save
{
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Magento\Framework\Controller\Result\RedirectFactory
	 */
	protected $_redirectResultFactory;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $_messageManager;

	/**
	 * @var \Magento\Sales\Model\AdminOrder\EmailSender
	 */
	protected $_emailSender;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	public function __construct(
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Framework\Controller\Result\RedirectFactory $redirectResultFactory,
			\Magento\Framework\Message\ManagerInterface $messageManager,
			\Magento\Sales\Model\AdminOrder\EmailSender $emailSender,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		$this->_objectManager = $objectManager;
		$this->_coreRegistry = $coreRegistry;
		$this->_redirectResultFactory = $redirectResultFactory;
		$this->_messageManager = $messageManager;
		$this->_emailSender = $emailSender;
		$this->_transactionFactory = $transactionFactory;
	}

	/**
	 * @param \Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject
	 * @param mixed $result
	 */
	public function aroundExecute(\Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject, \Closure $proceed)
	{
		$orderData = $subject->getRequest()->getPost('order');
		$sendConfirmation = isset($orderData['send_confirmation']) ? (int)$orderData['send_confirmation'] : 0;
		$orderParams = $subject->getRequest()->getParam('order');
		$orderParams['send_confirmation'] = 0;
		$subject->getRequest()->setPostValue('order', $orderParams);

		$this->_coreRegistry->register('realexcwcheckout_moto', true);
		$result = $proceed();
		if ($this->_messageManager->getMessages()->getLastAddedMessage() != null
				&& $this->_messageManager->getMessages()->getLastAddedMessage()->getType() == 'success') {
			$order = $this->_coreRegistry->registry('realexcw_checkout_last_order');
			if ($order instanceof \Magento\Sales\Model\Order) {
				if ($order->getPayment()->getMethodInstance() instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
					$this->_messageManager->getMessages()->clear();

					$transaction = $this->_transactionFactory->create()->loadByOrderId($order->getId());
					$transaction->setSendEmail($sendConfirmation);
					$transaction->save();

					return $this->_redirectResultFactory->create()->setPath('realexcw/checkout/payment', ['order_id' => $order->getId(), 'send_confirmation' => $sendConfirmation]);
				} elseif ($sendConfirmation) {
					$this->_emailSender->send($order);
				}
			}
		}
		return $result;
	}

	/**
	 * Retrieve order create model
	 *
	 * @return \Magento\Sales\Model\AdminOrder\Create
	 */
	private function _getOrderCreateModel()
	{
		return $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create');
	}
}