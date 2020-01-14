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

class ShippingMethods extends AbstractExternalCheckout
{
	/**
	 * @var string
	 */
	protected $_template = 'Customweb_RealexCw::externalcheckout/shippingmethods.phtml';

	/**
	 * @var array
	 */
	private $_rates;

	/**
	 * @var array
	 */
	private $errorMessages = [];

	/**
	 * @return array
	 */
	public function getShippingRates()
	{
		if (empty($this->_rates)) {
			$this->getAddress()->collectShippingRates()->save();
			$this->_rates = $this->getAddress()->getGroupedAllShippingRates();
		}

		return $this->_rates;
	}

	/**
	 * @return \Magento\Quote\Model\Quote\Address
	 */
	public function getAddress()
	{
		return $this->getContext()->getQuote()->getShippingAddress();
	}

	/**
	 * @param string $carrierCode
	 * @return string
	 */
	public function getCarrierName($carrierCode)
	{
		if ($name = $this->_scopeConfig->getValue(
				'carriers/' . $carrierCode . '/title',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		)
		) {
			return $name;
		}
		return $carrierCode;
	}

	/**
	 * @return string
	 */
	public function getAddressShippingMethod()
	{
		return $this->getAddress()->getShippingMethod();
	}

	/**
	 * Return the html text for shipping price
	 *
	 * @param \Magento\Quote\Model\Quote\Address\Rate $rate
	 * @return string
	 */
	public function getShippingPriceHtml(\Magento\Quote\Model\Quote\Address\Rate $rate)
	{
		/** @var \Magento\Checkout\Block\Shipping\Price $block */
		$block = $this->getLayout()->createBlock('Magento\Checkout\Block\Shipping\Price', 'shipping.price.' . $rate->getCode());
		$block->setTemplate('Magento_Checkout::shipping/price.phtml');
		$block->setShippingRate($rate);
		return $block->toHtml();
	}

	/**
	 * @param string|array $errorMessages
	 * @return \Customweb\RealexCw\Block\ExternalCheckout\ShippingMethods
	 */
	public function setErrorMessage($errorMessages)
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
}