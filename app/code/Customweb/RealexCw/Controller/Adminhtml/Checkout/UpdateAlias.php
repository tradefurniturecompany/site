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

namespace Customweb\RealexCw\Controller\Adminhtml\Checkout;

class UpdateAlias extends \Customweb\RealexCw\Controller\Adminhtml\Checkout
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $_resultJsonFactory;

	/**
	 * @var \Magento\Framework\View\LayoutInterface
	 */
	protected $_layout;

	/**
	 * @var \Magento\Payment\Helper\Data
	 */
	protected $_paymentHelper;

	/**
	 * @var \Customweb\RealexCw\Model\Payment\Method\ConfigProvider
	 */
	protected $_configProvider;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Catalog\Helper\Product $productHelper
	 * @param \Magento\Framework\Escaper $escaper
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Framework\View\LayoutInterface $layout
	 * @param \Magento\Payment\Helper\Data $paymentHelper
	 * @param \Customweb\RealexCw\Model\Payment\Method\ConfigProvider $configProvider
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Catalog\Helper\Product $productHelper,
			\Magento\Framework\Escaper $escaper,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
			\Magento\Framework\View\LayoutInterface $layout,
			\Magento\Payment\Helper\Data $paymentHelper,
			\Customweb\RealexCw\Model\Payment\Method\ConfigProvider $configProvider
	) {
		parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory, $transactionFactory);
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_layout = $layout;
		$this->_paymentHelper = $paymentHelper;
		$this->_configProvider = $configProvider;
	}

	public function execute()
	{
		$transaction = $this->_transactionFactory->create()->load($this->getRequest()->getParam('transactionId'));
		if (!$transaction->getId()) {
			throw new \Exception('The transaction has not been found.');
		}

		/* @var $block \Customweb\RealexCw\Block\Adminhtml\Checkout\Payment\Form */
		$block = $this->_layout->createBlock('Customweb\RealexCw\Block\Adminhtml\Checkout\Payment\Form');
		$block->setTransaction($transaction);
		$result = [
			'html' => $block->getFormRenderer()->renderForm($block->getForm())
		];
		return $this->_resultJsonFactory->create()->setData($result);
	}
}