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

namespace Customweb\RealexCw\Controller\Checkout;

class Success extends \Customweb\RealexCw\Controller\Checkout
{
	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Notification
	 */
	protected $_notification;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory
	 * @param \Customweb\RealexCw\Model\Authorization\Notification $notification
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\RealexCw\Model\Authorization\Method\Factory $authorizationMethodFactory,
			\Customweb\RealexCw\Model\Authorization\Notification $notification
	) {
		parent::__construct($context, $resultPageFactory, $checkoutSession, $quoteRepository, $transactionFactory, $authorizationMethodFactory);
		$this->_notification = $notification;
	}

	public function execute()
	{
		$transactionId = $this->getRequest()->getParam('cstrxid');
		try {
			$this->_notification->waitForNotification($transactionId);
			$this->handleSuccess($this->getTransaction($transactionId));
			return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success', ['_secure' => true]);
		} catch (\Exception $e) {
			return $this->handleFailure($this->getTransaction($transactionId), $e->getMessage());
		}
	}
}