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

namespace Customweb\RealexCw\Controller\Adminhtml;

abstract class Checkout extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Catalog\Helper\Product $productHelper
	 * @param \Magento\Framework\Escaper $escaper
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Catalog\Helper\Product $productHelper,
			\Magento\Framework\Escaper $escaper,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory);
		$this->_transactionFactory = $transactionFactory;
	}

	/**
	 * @param int $transactionId
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 * @throws \Exception
	 */
	protected function getTransaction($transactionId)
	{
		$transaction = $this->_transactionFactory->create()->load($transactionId);
		if (!$transaction->getId()) {
			throw new \Exception('The transaction has not been found.');
		}
		return $transaction;
	}
}