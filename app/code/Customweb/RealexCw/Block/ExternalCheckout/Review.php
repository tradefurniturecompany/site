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

namespace Customweb\RealexCw\Block\ExternalCheckout;

class Review extends \Magento\Sales\Block\Items\AbstractItems
{
	/**
	 * @var \Magento\Quote\Api\CartTotalRepositoryInterface
	 */
	protected $_cartTotalRepository;

	/**
	 * @var \Magento\Directory\Model\CurrencyFactory
	 */
	protected $_currencyFactory;

	/**
	 * @var string
	 */
	protected $_template = 'Customweb_RealexCw::externalcheckout/review.phtml';

	/**
	 * @var boolean
	 */
	private $renderConfirmationElements;

	/**
	 * @var \Magento\Directory\Model\Currency
	 */
	private $currency;

	/**
	 * @var array
	 */
	private $errorMessages = [];

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
	 * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
			\Magento\Directory\Model\CurrencyFactory $currencyFactory,
			array $data = []
	) {
		parent::__construct($context);
		$this->_cartTotalRepository = $cartTotalRepository;
		$this->_currencyFactory = $currencyFactory;
	}

	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->getContext()->getQuote()->getAllVisibleItems();
	}

	/**
	 * @var boolean
	 */
	public function getRenderConfirmationFormElements()
	{
		return $this->renderConfirmationElements;
	}

	/**
	 * @param boolean $renderConfirmationElements
	 * @return \Customweb\RealexCw\Block\ExternalCheckout\Review
	 */
	public function setRenderConfirmationFormElements($renderConfirmationElements)
	{
		$this->renderConfirmationElements = $renderConfirmationElements;
		return $this;
	}

	/**
	 * @return \Magento\Quote\Api\Data\TotalsInterface
	 */
	public function getTotals()
	{
		return $this->_cartTotalRepository->get($this->getContext()->getQuote()->getId());
	}

	/**
	 * @param string|array $errorMessages
	 * @return \Customweb\RealexCw\Block\ExternalCheckout\Review
	 */
	public function setErrorMessages($errorMessages)
	{
		if (empty($errorMessages)) {
			return $this;
		}
		if (!is_array($errorMessages)) {
			$errorMessages = [$errorMessages];
		}
		$this->errorMessages = $errorMessages;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrorMessages()
	{
		return $this->errorMessages;
	}

	protected function _beforeToHtml()
	{
		$this->getChildBlock('totals')->setContext($this->getContext());
		return $this;
	}
}