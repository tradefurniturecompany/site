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

namespace Customweb\RealexCw\Block\Payment\Method;

class Info extends \Magento\Payment\Block\Info
{
	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * Payment method form template
	 *
	 * @var string
	 */
	protected $_template = 'Customweb_RealexCw::payment/method/info.phtml';

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private $transaction = null;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->_transactionFactory = $transactionFactory;
	}

	public function getMethod()
	{
		$method = parent::getMethod();
		if ($this->getTransaction() instanceof \Customweb\RealexCw\Model\Authorization\Transaction) {
			$method->setStore($this->getTransaction()->getStoreId());
		}
		return $method;
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function getTransaction()
	{
		if (!($this->transaction instanceof \Customweb\RealexCw\Model\Authorization\Transaction)) {
			if ($this->getInfo() instanceof \Magento\Sales\Model\Order\Payment) {
				$transaction = $this->_transactionFactory->create()->loadByOrderId($this->getInfo()->getOrder()->getId());
				if ($transaction->getId()) {
					$this->transaction = $transaction;
				}
			}
		}
		return $this->transaction;
	}

	/**
	 * Get transaction view url.
	 *
	 * @return string
	 */
	public function getTransactionViewUrl()
	{
		return $this->getUrl('realexcw/transaction/view', ['transaction_id' => $this->getTransaction()->getId()]);
	}

	/**
	 * @return boolean
	 */
	public function isShowMethodImage()
	{
		return $this->getMethod()->isShowImage();
	}
}
