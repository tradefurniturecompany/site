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

namespace Customweb\RealexCw\Helper;

class FoomanSurcharge extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 */
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		parent::__construct($context);
		$this->_objectManager = $objectManager;
	}

	/**
	 * Returns whether the fooman surcharge plugin is active.
	 *
	 * @return boolean
	 */
	public function isModuleActive()
	{
		return $this->_moduleManager->isEnabled('Fooman_Surcharge');
	}

	/**
	 * Returns the surcharge amount for the given quote.
	 *
	 * @return float
	 */
	public function getQuoteSurchargeAmount($quoteId)
	{
		if (!$this->isModuleActive()) {
			return 0;
		}
		/* @var \Fooman\Totals\Model\QuoteAddressTotalManagement $management */
		$management = $this->_objectManager->get('Fooman\Totals\Model\QuoteAddressTotalManagement');
		$items = $management->getByQuoteId($quoteId);
		return $this->calculateAmount($items);
	}

	/**
	 * Returns the surcharge amount for the given order.
	 *
	 * @return float
	 */
	public function getOrderSurchargeAmount(\Magento\Sales\Model\Order $order)
	{
		if (!$this->isModuleActive()) {
			return 0;
		}
		/* @var \Fooman\Totals\Model\OrderTotalManagement $management */
		$management = $this->_objectManager->get('Fooman\Totals\Model\OrderTotalManagement');
		$items = $management->getByOrderId($order->getId());
		return $this->calculateAmount($items);
	}

	/**
	 * Returns the surcharge amount for the given invoice.
	 *
	 * @return float
	 */
	public function getInvoiceSurchargeAmount(\Magento\Sales\Model\Order\Invoice $invoice)
	{
		if (!$this->isModuleActive()) {
			return 0;
		}
		/* @var \Fooman\Totals\Model\InvoiceTotalManagement $management */
		$management = $this->_objectManager->get('Fooman\Totals\Model\InvoiceTotalManagement');
		$items = $management->getByInvoiceId($invoice->getId());
		return $this->calculateAmount($items);
	}

	/**
	 * Returns the surcharge amount for the given creditmemo.
	 *
	 * @return float
	 */
	public function getCreditmemoSurchargeAmount(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
	{
		if (!$this->isModuleActive()) {
			return 0;
		}
		/* @var \Fooman\Totals\Model\CreditmemoTotalManagement $management */
		$management = $this->_objectManager->get('Fooman\Totals\Model\CreditmemoTotalManagement');
		$items = $management->getByCreditmemoId($creditmemo->getId());
		return $this->calculateAmount($items);
	}

	/**
	 * @param \Fooman\Totals\Api\Data\TotalInterface[] $items
	 */
	private function calculateAmount($items) {
		if (!$items) {
			return 0;
		}
		$amount = 0;
		foreach ($items as $item) {
			if ($item->getCode() == 'fooman_surcharge') {
				$amount += $item->getAmount();
			}
		}
		return $amount;
	}

}